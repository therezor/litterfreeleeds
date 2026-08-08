<?php

namespace Database\Seeders;

use App\Models\CommunityPick;
use App\Models\Postcode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Sample picks for local development. Depends on LeedsPostcodeSeeder having run.
 *
 * Picks show no ward until `postcodes:import` has supplied ward codes — the
 * seeded postcodes carry none. Assigning one at random would be worse: a demo
 * pick in LS8 labelled "Bramley and Stanningley" reads as a bug in the ward
 * matching rather than as missing data.
 */
class DemoPicksSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent, so re-running db:seed locally does not pile up another
        // sixteen picks and two more coordinators every time.
        if (CommunityPick::query()->exists()) {
            $this->command?->info('Demo picks already seeded — skipping.');

            return;
        }

        $postcodes = Postcode::query()->pluck('postcode');

        if ($postcodes->isEmpty()) {
            $this->command?->warn('No postcodes found — run LeedsPostcodeSeeder or postcodes:import first.');

            return;
        }

        $coordinators = User::factory()->count(2)->create();
        $coordinators->each->assignRole($this->coordinatorRole());

        foreach ([['count' => 10, 'past' => false], ['count' => 6, 'past' => true]] as $batch) {
            CommunityPick::factory()
                ->count($batch['count'])
                ->when($batch['past'], fn ($factory) => $factory->past())
                ->recycle($coordinators)
                ->sequence(fn ($sequence): array => [
                    'postcode' => $postcodes[$sequence->index % $postcodes->count()],
                ])
                ->create();
        }
    }

    /**
     * A role that can run picks but nothing else — both a realistic starting
     * point and what makes a user eligible as a responsible person, since that
     * field only offers users who hold a role.
     */
    private function coordinatorRole(): Role
    {
        $role = Role::findOrCreate('Pick Coordinator', 'web');

        $role->syncPermissions(Permission::query()->where(function ($query): void {
            $query->where('name', 'like', '%:CommunityPick');
        })->get());

        return $role;
    }
}
