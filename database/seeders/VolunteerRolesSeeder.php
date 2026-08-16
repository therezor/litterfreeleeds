<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * The three onboarding roles.
 *
 * Deliberately separate from ShieldSeeder, which is a generated snapshot —
 * `shield:seeder --force` rewrites that file wholesale, so anything hand-added
 * there is lost the next time a Filament resource is added. This seeder is
 * idempotent (findOrCreate + syncPermissions) and safe to re-run.
 */
class VolunteerRolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // "How am I doing" — everyone who picks litter sees these, whatever
        // else their role lets them do.
        $personalWidgets = [
            Permission::findOrCreate('View:PersonalStatsOverview', 'web'),
            Permission::findOrCreate('View:PersonalActivityChart', 'web'),
            Permission::findOrCreate('View:PersonalWeekdayChart', 'web'),
        ];

        // Volunteers sign in and land on the dashboard. They get the personal
        // widgets and nothing else — no resource permissions, so every
        // navigation item stays hidden and the panel is just their own stats.
        Role::findOrCreate(User::ROLE_PICKER, 'web')->syncPermissions($personalWidgets);

        $onboard = Permission::findOrCreate('Onboard:User', 'web');

        // Onboard:User is the whole job: it gates the Onboarding resource, which
        // is where a bag holder sees their volunteers and confirms contact.
        //
        // Deliberately no ViewAny:User, so the general Users list stays hidden
        // from them — the Onboarding resource shows the same people with the
        // right verbs. And pointedly no Update:User: UserForm exposes a roles
        // multi-select and a password field, so that permission on a scoped
        // record set is a direct route to granting yourself Super Admin.
        Role::findOrCreate(User::ROLE_BAG_HOLDER, 'web')->syncPermissions([
            $onboard,
            Permission::findOrCreate('ViewAny:CommunityPick', 'web'),
            Permission::findOrCreate('View:CommunityPick', 'web'),
            // Bag holders pick litter too — same dashboard as everyone else.
            ...$personalWidgets,
        ]);

        // The same, unscoped (see User::seesAllVolunteers), plus the Users list
        // and running picks. Still no Update:User, for the reason above — which
        // does mean an organiser cannot assign a bag holder to an unmatched
        // volunteer; only a Super Admin can.
        Role::findOrCreate(User::ROLE_ORGANISER, 'web')->syncPermissions([
            Permission::findOrCreate('ViewAny:User', 'web'),
            Permission::findOrCreate('View:User', 'web'),
            $onboard,
            ...$personalWidgets,
            ...Permission::query()->where('name', 'like', '%:CommunityPick')->get(),
        ]);

        // ShieldSeeder's snapshot predates Onboard:User, and
        // super_admin.define_via_gate is false — the Super Admin role holds
        // every permission explicitly rather than bypassing via Gate::before.
        // Without this line a Super Admin could not onboard anyone.
        Role::findByName((string) config('filament-shield.super_admin.name'), 'web')
            ->givePermissionTo($onboard);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
