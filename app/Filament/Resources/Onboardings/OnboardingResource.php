<?php

namespace App\Filament\Resources\Onboardings;

use App\Filament\Resources\Onboardings\Pages\ListOnboardings;
use App\Filament\Resources\Onboardings\Pages\ViewOnboarding;
use App\Filament\Resources\Onboardings\Schemas\OnboardingInfolist;
use App\Filament\Resources\Onboardings\Tables\OnboardingsTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * A Purple Bag Holder's working list: the volunteers matched to them who are
 * still waiting to be contacted.
 *
 * A second resource over the User model rather than a filter on UserResource,
 * because the two have different audiences and different verbs. This one is
 * read-plus-confirm — no creating, no editing, no deleting. Changing an
 * onboarding date after the fact is an admin correction and lives on the user
 * record itself.
 *
 * Excluded from Shield (see config/filament-shield.php): Shield names
 * permissions after the model, so a second resource over User would generate a
 * colliding set. Access runs off the existing Onboard:User permission instead.
 */
class OnboardingResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'onboarding';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'volunteer';

    protected static ?string $navigationLabel = 'Onboarding';

    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return OnboardingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OnboardingsTable::configure($table);
    }

    /**
     * Volunteers only, narrowed to the ones this viewer looks after. A bag
     * holder sees those matched to them; organisers and admins see everyone.
     *
     * Onboarded volunteers are excluded by default rather than by query, so the
     * "Show onboarded" filter can bring them back when a holder needs to look
     * one up.
     */
    public static function getEloquentQuery(): Builder
    {
        $viewer = Filament::auth()->user();

        $query = parent::getEloquentQuery()
            ->whereHas('roles', fn (Builder $roles) => $roles->where('name', User::ROLE_PICKER));

        if ($viewer instanceof User) {
            User::applyVisibility($query, $viewer);
        }

        return $query;
    }

    /**
     * The nav badge is the point of this resource — a bag holder should be able
     * to tell at a glance whether anyone is waiting on them.
     *
     * Counts confirmed volunteers only. A holder is emailed on verification,
     * not on registration, so counting unconfirmed sign-ups would make the
     * badge claim more people are waiting than the holder has been told about,
     * and send them chasing addresses nobody has proved are real. Unconfirmed
     * volunteers still appear in the list, flagged as such.
     */
    public static function getNavigationBadge(): ?string
    {
        $waiting = static::getEloquentQuery()
            ->whereNotNull('email_verified_at')
            ->whereNull('onboarded_at')
            ->count();

        return $waiting > 0 ? (string) $waiting : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool
    {
        $viewer = Filament::auth()->user();

        return $viewer instanceof User && $viewer->can('Onboard:User');
    }

    public static function canView(mixed $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(mixed $record): bool
    {
        return false;
    }

    public static function canDelete(mixed $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOnboardings::route('/'),
            'view' => ViewOnboarding::route('/{record}'),
        ];
    }
}
