<?php

namespace App\Models;

use App\Models\Concerns\HasCoordinates;
use App\Notifications\WelcomeVolunteer;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $postcode Normalised — uppercase, no space.
 * @property string|null $outward_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $terms_accepted_at
 * @property Carbon|null $onboarded_at
 * @property int|null $assigned_bag_holder_id
 * @property-read float|null $distance_miles Only present when the query used withDistanceFrom().
 *
 * Note on #[Fillable]: assigned_bag_holder_id, onboarded_at and
 * terms_accepted_at are deliberately absent. /join is an open public POST, and
 * a broad fillable list is exactly how a stranger would self-assign a bag
 * holder or mark themselves onboarded. RegisterVolunteer sets all three
 * explicitly with forceFill.
 */
#[Fillable(['name', 'email', 'password', 'postcode'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasCoordinates, HasFactory, HasRoles, Notifiable;

    /**
     * Volunteers who sign up at /join. Holds no permissions — it exists so a
     * new volunteer is a known quantity rather than a roleless stranger, and so
     * the responsible-person select can keep filtering on whereHas('roles').
     */
    public const ROLE_PICKER = 'Picker';

    /**
     * Receives and hands out purple bags locally, and is the human who makes
     * first contact with a new volunteer.
     */
    public const ROLE_BAG_HOLDER = 'Purple Bag Holder';

    /**
     * Runs a group: sees every volunteer and every bag holder, can onboard any
     * of them, and can re-run the match to the nearest holder.
     *
     * Note they deliberately do NOT hold Update:User, so they cannot hand-pick
     * a specific bag holder — that field lives on UserForm alongside the roles
     * multi-select and the password, and Update:User on those is a route to
     * granting yourself Super Admin. Manual reassignment is admin-only until
     * those fields are separately gated.
     */
    public const ROLE_ORGANISER = 'Group Organiser';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'terms_accepted_at' => 'datetime',
            'onboarded_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Anyone holding a role gets in, volunteers included — what they can
     * actually see is decided by permissions, and a picker holds none, so they
     * land on the dashboard and nothing else.
     *
     * Without this method Filament's Authenticate middleware falls back to
     * `config('app.env') !== 'local'`, which 403s everyone in production.
     *
     * Roleless accounts stay out. Every registration assigns Picker, so the
     * only users without a role are legacy rows from before onboarding existed.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles->isNotEmpty();
    }

    /**
     * Whether this user sees every volunteer, or only their own.
     *
     * Phrased as a narrowing of bag holders specifically, not a whitelist of
     * privileged roles. Scoping every role that isn't Organiser or Super Admin
     * would silently blind any bespoke admin role someone creates later — the
     * one group that genuinely needs a partial view is bag holders, so they are
     * the only group that gets one.
     *
     * Kept off the policy so it can be tested directly and reused by the
     * resource query, which a policy cannot filter.
     */
    public function seesAllVolunteers(): bool
    {
        if (! $this->hasRole(self::ROLE_BAG_HOLDER)) {
            return true;
        }

        return $this->hasRole([
            (string) config('filament-shield.super_admin.name'),
            self::ROLE_ORGANISER,
        ]);
    }

    /**
     * Volunteers verify through the public site, never the panel: Filament's
     * verification routes sit behind its Authenticate middleware, so a Picker
     * — who by design cannot access the panel — would be 403'd on the link and
     * could never verify at all.
     *
     * Staff keep the stock notification. This method is also what Filament's
     * "resend" action calls, and an organiser should not be told their local
     * bag holder will be in touch.
     */
    public function sendEmailVerificationNotification(): void
    {
        if (! $this->hasRole(self::ROLE_PICKER)) {
            parent::sendEmailVerificationNotification();

            return;
        }

        $this->notify(new WelcomeVolunteer);
    }

    /**
     * Picks this user is the responsible person for.
     */
    public function communityPicks(): HasMany
    {
        return $this->hasMany(CommunityPick::class, 'responsible_user_id');
    }

    /**
     * The Purple Bag Holder matched to this volunteer at registration.
     */
    public function assignedBagHolder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'assigned_bag_holder_id');
    }

    /**
     * Volunteers matched to this bag holder.
     */
    public function assignedPickers(): HasMany
    {
        return $this->hasMany(self::class, 'assigned_bag_holder_id');
    }

    public function postcodeRecord(): BelongsTo
    {
        return $this->belongsTo(Postcode::class, 'postcode', 'postcode');
    }

    /**
     * Bag holders we can actually measure a distance to. Holders are created by
     * an admin in the panel rather than through /join, so one can exist with no
     * postcode — and a null latitude would sort to the front of the distance
     * query and win every match.
     */
    #[Scope]
    protected function purpleBagHolders(Builder $query): void
    {
        // whereHas rather than Spatie's role() scope: role() throws
        // RoleDoesNotExist when the role has not been seeded, and "nobody is a
        // bag holder yet" is a legitimate state — it is day one.
        $query
            ->whereHas('roles', fn (Builder $roles) => $roles->where('name', self::ROLE_BAG_HOLDER))
            ->whereNotNull('latitude');
    }

    /**
     * Narrows a user list to what the viewer is allowed to see: a bag holder
     * sees exactly the volunteers matched to them.
     *
     * Strictly the volunteers, not the viewer's own row. A bag holder is very
     * often a picker too, and including self would put them in their own
     * onboarding queue — inviting them to confirm they had contacted
     * themselves. UserPolicy::isVisibleTo() must agree with this.
     *
     * A plain static rather than a #[Scope], because the caller that matters —
     * UserResource::getEloquentQuery() — receives a Builder<Model> from its
     * parent, and a magic scope call on that is untypeable at PHPStan level 5.
     *
     * @param  Builder<Model>  $query
     */
    public static function applyVisibility(Builder $query, self $viewer): void
    {
        if ($viewer->seesAllVolunteers()) {
            return;
        }

        $query->where('users.assigned_bag_holder_id', $viewer->getKey());
    }

    protected function formattedPostcode(): Attribute
    {
        return Attribute::get(fn (): ?string => filled($this->postcode)
            ? Postcode::format((string) $this->postcode)
            : null);
    }

    public function hasBeenOnboarded(): bool
    {
        return $this->onboarded_at !== null;
    }
}
