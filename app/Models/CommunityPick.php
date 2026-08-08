<?php

namespace App\Models;

use App\Observers\CommunityPickObserver;
use Carbon\CarbonImmutable;
use Database\Factories\CommunityPickFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

/**
 * A community litter pick: when and where to meet, and who is running it.
 *
 * Note there is deliberately no #[RouteKey('slug')] — Filament resolves admin
 * records through getRouteKeyName() too, so that attribute would rewrite every
 * /app URL as well. The public route binds by slug explicitly instead.
 */
#[Fillable([
    'name',
    'slug',
    'date',
    'time_from',
    'time_to',
    'excerpt',
    'description',
    'location',
    'postcode',
    'responsible_user_id',
])]
#[ObservedBy(CommunityPickObserver::class)]
class CommunityPick extends Model
{
    /** @use HasFactory<CommunityPickFactory> */
    use HasFactory;

    /**
     * Leeds is in Europe/London but config('app.timezone') is UTC. Deciding
     * "is this pick still upcoming?" in UTC would resurrect yesterday's picks
     * for the first hour of every British Summer Time morning.
     */
    public const DISPLAY_TIMEZONE = 'Europe/London';

    /**
     * time_from and time_to are deliberately uncast. Eloquent has no `time`
     * cast; casting to datetime would attach today's date to a wall-clock time
     * and serialise a full ISO timestamp. Uncast, PDO returns "09:30:00" and it
     * round-trips byte for byte.
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function postcodeRecord(): BelongsTo
    {
        return $this->belongsTo(Postcode::class, 'postcode', 'postcode');
    }

    /**
     * The electoral ward the pick falls in — read through the postcode rather
     * than copied onto the pick, so it is always whatever the current ONSPD
     * release says and postcodes:import never has to touch this table.
     */
    public function ward(): HasOneThrough
    {
        return $this->hasOneThrough(
            Ward::class,
            Postcode::class,
            'postcode',   // key on postcodes matching...
            'code',       // key on wards matching...
            'postcode',   // ...this key on community_picks
            'ward_code',  // ...this key on postcodes
        );
    }

    /**
     * The local authority district — the town or city. Read through the
     * postcode, same as the ward.
     */
    public function district(): HasOneThrough
    {
        return $this->hasOneThrough(
            District::class,
            Postcode::class,
            'postcode',
            'code',
            'postcode',
            'district_code',
        );
    }

    /**
     * "Headingley and Hyde Park, Leeds" — ward then city, always both when we
     * have them. Volunteers do not all know which ward is which, and the city
     * is what makes a pick outside Leeds obvious at a glance.
     */
    protected function placeLabel(): Attribute
    {
        return Attribute::get(fn (): ?string => collect([$this->ward?->name, $this->district?->name])
            ->filter()
            ->implode(', ') ?: null);
    }

    /**
     * Today in Leeds, as a Y-m-d string to compare against the date column.
     */
    public static function todayInLeeds(): string
    {
        return CarbonImmutable::now(self::DISPLAY_TIMEZONE)->toDateString();
    }

    #[Scope]
    protected function upcoming(Builder $query): void
    {
        // Plain where, not whereDate: the column is already a date, and
        // whereDate would compile to "date"::date and skip the index.
        $query->where('date', '>=', self::todayInLeeds())
            ->orderBy('date')
            ->orderBy('time_from');
    }

    #[Scope]
    protected function past(Builder $query): void
    {
        $query->where('date', '<', self::todayInLeeds())
            ->orderByDesc('date')
            ->orderByDesc('time_from');
    }

    /**
     * Adds a distance_miles column measured from the given point. Haversine over
     * a few dozen rows — no spatial extension, no bounding box. The clamp keeps
     * acos() from returning NaN when floating point overshoots 1 for a pick
     * sitting on the exact point being searched from.
     *
     * Callers wanting distance to be the primary sort must reorder() first —
     * the upcoming() scope has already applied a date ordering.
     */
    #[Scope]
    protected function withDistanceFrom(Builder $query, float $latitude, float $longitude): void
    {
        // selectRaw only appends, so without this the raw expression would be
        // the entire select list when no columns have been chosen yet.
        if (blank($query->getQuery()->columns)) {
            $query->select($this->getTable().'.*');
        }

        $query->selectRaw(
            '3959 * acos(least(1.0, greatest(-1.0,'
            .' cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))'
            .' + sin(radians(?)) * sin(radians(latitude))'
            .'))) as distance_miles',
            [$latitude, $longitude, $latitude]
        )->withCasts(['distance_miles' => 'float']);
    }

    /**
     * "10:00 – 12:30". String slicing rather than Carbon, so no timezone can
     * ever be applied to what is a wall-clock time.
     */
    protected function timeRange(): Attribute
    {
        return Attribute::get(fn (): string => sprintf(
            '%s – %s',
            Str::substr((string) $this->time_from, 0, 5),
            Str::substr((string) $this->time_to, 0, 5),
        ))->shouldCache();
    }

    protected function formattedPostcode(): Attribute
    {
        return Attribute::get(fn (): string => Postcode::format((string) $this->postcode));
    }

    protected function directionsUrl(): Attribute
    {
        return Attribute::get(fn (): string => (string) Uri::of('https://www.google.com/maps/dir/')
            ->withQuery([
                'api' => '1',
                'destination' => $this->formatted_postcode.', UK',
            ]));
    }

    /**
     * Slugs include the date because picks recur — "Roundhay Park Pick" happens
     * every month. Generated once on create and never regenerated, so a
     * published link keeps working after a rename.
     */
    public static function uniqueSlug(string $name, mixed $date): string
    {
        $base = Str::slug($name.' '.CarbonImmutable::parse($date)->format('Y-m-d'));
        $slug = $base;

        for ($suffix = 2; self::query()->where('slug', $slug)->exists(); $suffix++) {
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }
}
