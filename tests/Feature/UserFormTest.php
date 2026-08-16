<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\Postcode;
use App\Models\User;
use Database\Seeders\VolunteerRolesSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserFormTest extends TestCase
{
    private function admin(): User
    {
        Role::findOrCreate((string) config('filament-shield.super_admin.name'), 'web');
        $this->seed(VolunteerRolesSeeder::class);

        $role = Role::findOrCreate('Test Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('ViewAny:User', 'web'),
            Permission::findOrCreate('Create:User', 'web'),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return User::factory()->create()->assignRole($role);
    }

    private function bagHolderRoleId(): int
    {
        return (int) Role::findByName(User::ROLE_BAG_HOLDER, 'web')->getKey();
    }

    /**
     * The silent failure this guards against: a bag holder with no postcode is
     * filtered out of the nearest-holder query by whereNotNull('latitude'), so
     * they would never be matched with anyone and nobody would know why.
     */
    public function test_a_bag_holder_must_have_a_postcode(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Priya Holder',
                'email' => 'priya@example.com',
                'password' => 'correct-horse-battery',
                'roles' => [$this->bagHolderRoleId()],
                'postcode' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['postcode']);
    }

    public function test_a_bag_holder_with_a_postcode_saves(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        Livewire::actingAs($this->admin())
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Priya Holder',
                'email' => 'priya@example.com',
                'password' => 'correct-horse-battery',
                'roles' => [$this->bagHolderRoleId()],
                'postcode' => 'LS6 2AB',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $holder = User::query()->where('email', 'priya@example.com')->sole();

        $this->assertSame('LS62AB', $holder->postcode);
        $this->assertSame(53.8155, $holder->latitude);
    }

    /**
     * Everyone else is free to have no postcode — most admins never will.
     */
    public function test_a_non_bag_holder_needs_no_postcode(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Plain Admin',
                'email' => 'plain@example.com',
                'password' => 'correct-horse-battery',
                'roles' => [],
                'postcode' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_it_rejects_a_postcode_that_is_not_in_the_directory(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Priya Holder',
                'email' => 'priya@example.com',
                'password' => 'correct-horse-battery',
                'roles' => [$this->bagHolderRoleId()],
                'postcode' => 'LS99 9ZZ',
            ])
            ->call('create')
            ->assertHasFormErrors(['postcode']);
    }
}
