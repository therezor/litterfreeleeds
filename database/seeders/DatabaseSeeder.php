<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters: postcodes must exist before any pick can be created.
     */
    public function run(): void
    {
        $testUser = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->raw(['name' => 'Test User', 'email' => 'test@example.com', 'password' => '12345678']),
        );

        // A committed snapshot of the roles and permissions Shield generates, so
        // a fresh clone and CI get them without running shield:generate. Re-run
        // `shield:seeder --force` after adding any Filament resource.
        $this->call([
            ShieldSeeder::class,
            // After ShieldSeeder: it grants Onboard:User to the Super Admin
            // role, which ShieldSeeder's snapshot creates.
            VolunteerRolesSeeder::class,
            LeedsPostcodeSeeder::class,
        ]);

        if (app()->environment('local')) {
            // ShieldSeeder brings the Super Admin role but deliberately no users,
            // so without this a migrate:fresh --seed leaves nobody able to
            // administer the panel.
            $testUser->assignRole(config('filament-shield.super_admin.name'));

            $this->call(DemoPicksSeeder::class);
        }
    }
}
