<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Postcode;
use App\Models\User;
use App\Notifications\WelcomeVolunteer;
use Database\Seeders\VolunteerRolesSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserResourceScopingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The seeder needs a Super Admin role to grant Onboard:User to.
        Role::findOrCreate((string) config('filament-shield.super_admin.name'), 'web');
        $this->seed(VolunteerRolesSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();
    }

    private function bagHolder(): User
    {
        return User::factory()->bagHolder()->atPostcode('LS6 2AB')->create();
    }

    private function pickerFor(?User $holder): User
    {
        $picker = User::factory()->picker()->atPostcode('LS6 2AB')->create();

        if ($holder !== null) {
            $picker->forceFill(['assigned_bag_holder_id' => $holder->getKey()])->save();
        }

        return $picker;
    }

    public function test_a_bag_holder_cannot_onboard_someone_elses_volunteer(): void
    {
        $holder = $this->bagHolder();
        $theirs = $this->pickerFor($this->bagHolder());

        $this->assertFalse($holder->can('onboard', $theirs));
    }

    /**
     * The reason onboarding is its own permission: a bag holder holding
     * Update:User could give themselves Super Admin through the roles field on
     * UserForm, which scoping does nothing to prevent.
     */
    public function test_a_bag_holder_cannot_edit_users(): void
    {
        $holder = $this->bagHolder();
        $picker = $this->pickerFor($holder);

        $this->actingAs($holder);

        $this->assertFalse($holder->can('update', $picker));
        $this->assertFalse($holder->can('create', User::class));
        $this->assertFalse($holder->can('delete', $picker));
    }

    /**
     * Bag holders work out of the Onboarding resource, which shows the same
     * people with the right verbs. The general Users list is admin territory.
     */
    public function test_a_bag_holder_does_not_see_the_users_list(): void
    {
        Livewire::actingAs($this->bagHolder())
            ->test(ListUsers::class)
            ->assertForbidden();
    }

    public function test_a_group_organiser_still_sees_the_users_list(): void
    {
        $organiser = User::factory()->organiser()->create();
        $holder = $this->bagHolder();
        $picker = $this->pickerFor($holder);

        Livewire::actingAs($organiser)
            ->test(ListUsers::class)
            ->assertCanSeeTableRecords([$holder, $picker]);
    }

    /**
     * Volunteers sign in and land on the dashboard. They hold no resource
     * permissions, so the panel is their own stats and nothing else — the
     * Users list they are not allowed to see is a 403, not a filtered list.
     */
    public function test_a_picker_can_reach_the_panel_but_not_the_users_list(): void
    {
        $picker = $this->pickerFor($this->bagHolder());

        $this->assertTrue($picker->canAccessPanel(Filament::getPanel('app')));

        Livewire::actingAs($picker)
            ->test(ListUsers::class)
            ->assertForbidden();
    }

    public function test_bag_holders_and_organisers_can_access_the_panel(): void
    {
        $panel = Filament::getPanel('app');

        $this->assertTrue($this->bagHolder()->canAccessPanel($panel));
        $this->assertTrue(User::factory()->organiser()->create()->canAccessPanel($panel));

        // A holder who also picks is still staff.
        $this->assertTrue(
            User::factory()->bagHolder()->picker()->create()->canAccessPanel($panel)
        );
    }

    /**
     * The panel's ->emailVerification() plus MustVerifyEmail means an
     * unverified volunteer is held at the verification prompt — which carries
     * the resend button — rather than reaching the dashboard.
     */
    public function test_an_unverified_picker_is_sent_to_the_verification_prompt(): void
    {
        $picker = User::factory()->picker()->unverified()->create();

        $this->actingAs($picker)
            ->get('/app')
            ->assertRedirect(route('filament.app.auth.email-verification.prompt'));
    }

    public function test_the_verification_prompt_offers_a_resend(): void
    {
        $picker = User::factory()->picker()->unverified()->create();

        $this->actingAs($picker)
            ->get(route('filament.app.auth.email-verification.prompt'))
            ->assertOk()
            ->assertSee('Resend', false);
    }

    /**
     * The resend goes through User::sendEmailVerificationNotification(), so a
     * volunteer gets the welcome email with the instructions again rather than
     * a bare verification link.
     */
    public function test_resending_sends_the_volunteer_welcome_email(): void
    {
        Notification::fake();
        $picker = User::factory()->picker()->unverified()->create();

        $picker->sendEmailVerificationNotification();

        Notification::assertSentTo($picker, WelcomeVolunteer::class);
    }

    public function test_a_verified_picker_reaches_the_dashboard(): void
    {
        $picker = $this->pickerFor($this->bagHolder());

        $this->actingAs($picker)
            ->get('/app')
            ->assertSuccessful();
    }

    public function test_a_bag_holder_reaches_the_panel_over_http(): void
    {
        $this->actingAs($this->bagHolder())
            ->get('/app')
            ->assertSuccessful();
    }

    public function test_a_roleless_user_cannot_access_the_panel(): void
    {
        $this->assertFalse(
            User::factory()->create()->canAccessPanel(Filament::getPanel('app'))
        );
    }

    public function test_a_super_admin_can_onboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole((string) config('filament-shield.super_admin.name'));

        // ShieldSeeder's snapshot predates Onboard:User, so the seeder grants it.
        $this->assertTrue($admin->can('Onboard:User'));

        $picker = $this->pickerFor($this->bagHolder());
        $this->assertTrue($admin->can('onboard', $picker));
    }

    public function test_permissions_the_bag_holder_role_carries(): void
    {
        $holder = $this->bagHolder();

        // Created up front so the negative assertions below fail on the role
        // not carrying the permission, not on the name being unknown.
        Permission::findOrCreate('ViewAny:User', 'web');
        Permission::findOrCreate('Update:User', 'web');
        Permission::findOrCreate('Create:User', 'web');
        Permission::findOrCreate('Delete:User', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Onboard:User is the whole job — the Onboarding resource runs off it.
        $this->assertTrue($holder->can('Onboard:User'));
        $this->assertFalse($holder->can('ViewAny:User'));
        $this->assertFalse($holder->can('Update:User'));
        $this->assertFalse($holder->can('Create:User'));
        $this->assertFalse($holder->can('Delete:User'));
    }
}
