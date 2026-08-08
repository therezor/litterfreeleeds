<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\CommunityPick;
use App\Models\Postcode;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * community_picks.responsible_user_id restricts on delete, so deleting a
 * coordinator who still owns picks would be a raw database error. The Users
 * table catches it first.
 */
class UserDeletionTest extends TestCase
{
    private function userAdmin(): User
    {
        $role = Role::findOrCreate('User Admin', 'web');

        $role->syncPermissions(array_map(
            fn (string $permission) => Permission::findOrCreate($permission, 'web'),
            ['ViewAny:User', 'View:User', 'Update:User', 'Delete:User']
        ));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return User::factory()->create()->assignRole($role);
    }

    public function test_a_user_responsible_for_a_pick_is_not_deleted(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $coordinator = User::factory()->create();
        CommunityPick::factory()->atPostcode('LS6 2AB')->create([
            'responsible_user_id' => $coordinator->getKey(),
        ]);

        Livewire::actingAs($this->userAdmin())
            ->test(ListUsers::class)
            ->callAction(TestAction::make('delete')->table($coordinator));

        $this->assertModelExists($coordinator);
    }

    public function test_a_user_with_no_picks_is_deleted(): void
    {
        $spare = User::factory()->create();

        Livewire::actingAs($this->userAdmin())
            ->test(ListUsers::class)
            ->callAction(TestAction::make('delete')->table($spare));

        $this->assertModelMissing($spare);
    }
}
