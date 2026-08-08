<?php

namespace Tests\Feature;

use App\Filament\Resources\CommunityPicks\CommunityPickResource;
use App\Filament\Resources\CommunityPicks\Pages\CreateCommunityPick;
use App\Filament\Resources\CommunityPicks\Pages\ListCommunityPicks;
use App\Models\CommunityPick;
use App\Models\Postcode;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CommunityPickResourceTest extends TestCase
{
    private function userWithPermissions(string ...$permissions): User
    {
        $role = Role::findOrCreate('Test Role', 'web');

        $role->syncPermissions(array_map(
            fn (string $permission) => Permission::findOrCreate($permission, 'web'),
            $permissions
        ));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return User::factory()->create()->assignRole($role);
    }

    /**
     * Asserted through the Livewire page rather than a GET of /app/upcoming-picks,
     * because Filament's Authenticate middleware 403s every user whenever
     * APP_ENV is not "local" — User does not implement FilamentUser. That is a
     * panel-wide concern, not this resource's, so testing the HTTP route here
     * would pass for the wrong reason.
     */
    public function test_the_list_page_needs_the_view_any_permission(): void
    {
        Livewire::actingAs($this->userWithPermissions('ViewAny:CommunityPick'))
            ->test(ListCommunityPicks::class)
            ->assertOk();
    }

    public function test_a_user_without_the_permission_is_forbidden(): void
    {
        Livewire::actingAs($this->userWithPermissions('ViewAny:User'))
            ->test(ListCommunityPicks::class)
            ->assertForbidden();
    }

    public function test_the_navigation_item_is_hidden_without_the_permission(): void
    {
        $this->actingAs($this->userWithPermissions('ViewAny:User'));

        $this->assertFalse(CommunityPickResource::canViewAny());
        $this->assertFalse(CommunityPickResource::canCreate());
    }

    public function test_the_table_lists_picks(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $picks = CommunityPick::factory()->count(3)->atPostcode('LS6 2AB')->create();

        Livewire::actingAs($this->userWithPermissions('ViewAny:CommunityPick'))
            ->test(ListCommunityPicks::class)
            ->assertCanSeeTableRecords($picks);
    }

    public function test_it_creates_a_pick_and_derives_everything_from_the_postcode(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();
        $responsible = $this->userWithPermissions('ViewAny:CommunityPick', 'Create:CommunityPick');

        Livewire::actingAs($responsible)
            ->test(CreateCommunityPick::class)
            ->fillForm([
                'name' => 'Woodhouse Moor Morning Pick',
                'excerpt' => 'An hour on the moor before the football starts.',
                'date' => '2026-09-12',
                'time_from' => '10:00',
                'time_to' => '12:00',
                'location' => 'By the bandstand',
                'postcode' => 'ls6 2ab',
                'responsible_user_id' => $responsible->getKey(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $pick = CommunityPick::query()->sole();

        $this->assertSame('LS62AB', $pick->postcode);
        $this->assertSame('LS6', $pick->outward_code);
        $this->assertSame(53.8155, $pick->latitude);
        $this->assertSame('woodhouse-moor-morning-pick-2026-09-12', $pick->slug);
    }

    public function test_it_rejects_an_end_time_before_the_start_time(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $responsible = $this->userWithPermissions('ViewAny:CommunityPick', 'Create:CommunityPick');

        Livewire::actingAs($responsible)
            ->test(CreateCommunityPick::class)
            ->fillForm([
                'name' => 'Backwards Pick',
                'excerpt' => 'Ends before it starts.',
                'date' => '2026-09-12',
                'time_from' => '12:00',
                'time_to' => '10:00',
                'location' => 'By the bandstand',
                'postcode' => 'LS6 2AB',
                'responsible_user_id' => $responsible->getKey(),
            ])
            ->call('create')
            ->assertHasFormErrors(['time_to']);
    }

    public function test_it_rejects_a_postcode_that_is_not_in_the_directory(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $responsible = $this->userWithPermissions('ViewAny:CommunityPick', 'Create:CommunityPick');

        Livewire::actingAs($responsible)
            ->test(CreateCommunityPick::class)
            ->fillForm([
                'name' => 'Nowhere Pick',
                'excerpt' => 'Somewhere we cannot place.',
                'date' => '2026-09-12',
                'time_from' => '10:00',
                'time_to' => '12:00',
                'location' => 'Nowhere in particular',
                'postcode' => 'LS99 9ZZ',
                'responsible_user_id' => $responsible->getKey(),
            ])
            ->call('create')
            ->assertHasFormErrors(['postcode']);
    }

    public function test_it_rejects_something_that_is_not_a_postcode_at_all(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $responsible = $this->userWithPermissions('ViewAny:CommunityPick', 'Create:CommunityPick');

        Livewire::actingAs($responsible)
            ->test(CreateCommunityPick::class)
            ->fillForm([
                'name' => 'Nonsense Pick',
                'excerpt' => 'Not a postcode.',
                'date' => '2026-09-12',
                'time_from' => '10:00',
                'time_to' => '12:00',
                'location' => 'Nowhere in particular',
                'postcode' => 'banana',
                'responsible_user_id' => $responsible->getKey(),
            ])
            ->call('create')
            ->assertHasFormErrors(['postcode']);
    }

    public function test_only_users_with_a_role_can_be_made_responsible(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $withRole = $this->userWithPermissions('ViewAny:CommunityPick', 'Create:CommunityPick');
        $withoutRole = User::factory()->create();

        Livewire::actingAs($withRole)
            ->test(CreateCommunityPick::class)
            ->assertFormFieldExists(
                'responsible_user_id',
                fn ($field): bool => array_key_exists($withRole->getKey(), $field->getOptions())
                    && ! array_key_exists($withoutRole->getKey(), $field->getOptions()),
            );
    }
}
