<?php

namespace Tests\Feature;

use App\Filament\Resources\Onboardings\OnboardingResource;
use App\Filament\Resources\Onboardings\Pages\ListOnboardings;
use App\Filament\Resources\Onboardings\Pages\ViewOnboarding;
use App\Models\Postcode;
use App\Models\User;
use Database\Seeders\VolunteerRolesSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OnboardingResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_a_bag_holder_sees_only_the_volunteers_assigned_to_them(): void
    {
        $holder = $this->bagHolder();
        $mine = $this->pickerFor($holder);
        $theirs = $this->pickerFor($this->bagHolder());

        Livewire::actingAs($holder)
            ->test(ListOnboardings::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs]);
    }

    public function test_a_group_organiser_sees_every_volunteer(): void
    {
        $organiser = User::factory()->organiser()->create();
        $mine = $this->pickerFor($this->bagHolder());
        $theirs = $this->pickerFor($this->bagHolder());

        Livewire::actingAs($organiser)
            ->test(ListOnboardings::class)
            ->assertCanSeeTableRecords([$mine, $theirs]);
    }

    /**
     * The list is a to-do, so it holds volunteers only — never bag holders,
     * organisers or admins, even for a viewer who can see everyone.
     */
    public function test_it_lists_volunteers_only(): void
    {
        $organiser = User::factory()->organiser()->create();
        $holder = $this->bagHolder();
        $picker = $this->pickerFor($holder);

        Livewire::actingAs($organiser)
            ->test(ListOnboardings::class)
            ->assertCanSeeTableRecords([$picker])
            ->assertCanNotSeeTableRecords([$holder, $organiser]);
    }

    public function test_it_opens_on_volunteers_awaiting_contact(): void
    {
        $holder = $this->bagHolder();
        $waiting = $this->pickerFor($holder);
        $done = $this->pickerFor($holder);
        $done->forceFill(['onboarded_at' => now()])->save();

        Livewire::actingAs($holder)
            ->test(ListOnboardings::class)
            ->assertCanSeeTableRecords([$waiting])
            ->assertCanNotSeeTableRecords([$done]);
    }

    public function test_a_bag_holder_can_mark_their_volunteer_as_onboarded(): void
    {
        $holder = $this->bagHolder();
        $picker = $this->pickerFor($holder);

        Livewire::actingAs($holder)
            ->test(ListOnboardings::class)
            ->callAction(TestAction::make('onboard')->table($picker))
            ->assertHasNoActionErrors();

        $this->assertNotNull($picker->fresh()->onboarded_at);
    }

    public function test_the_onboard_action_is_hidden_once_onboarded(): void
    {
        $holder = $this->bagHolder();
        $picker = $this->pickerFor($holder);
        $picker->forceFill(['onboarded_at' => now()])->save();

        Livewire::actingAs($holder)
            ->test(ListOnboardings::class)
            // Onboarded rows are filtered out by default, so ask for them.
            ->filterTable('onboarded_at', true)
            ->assertActionHidden(TestAction::make('onboard')->table($picker));
    }

    public function test_a_bag_holder_cannot_open_someone_elses_volunteer(): void
    {
        $holder = $this->bagHolder();
        $theirs = $this->pickerFor($this->bagHolder());

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($holder)->test(ViewOnboarding::class, ['record' => $theirs->getKey()]);
    }

    public function test_a_bag_holder_can_open_their_own_volunteer(): void
    {
        $holder = $this->bagHolder();
        $mine = $this->pickerFor($holder);

        Livewire::actingAs($holder)
            ->test(ViewOnboarding::class, ['record' => $mine->getKey()])
            ->assertOk();
    }

    public function test_a_volunteer_cannot_see_the_onboarding_list(): void
    {
        $picker = $this->pickerFor($this->bagHolder());

        $this->actingAs($picker);

        $this->assertFalse(OnboardingResource::canViewAny());
    }

    public function test_the_resource_is_read_plus_confirm_only(): void
    {
        $this->actingAs($this->bagHolder());

        $this->assertFalse(OnboardingResource::canCreate());
        $this->assertTrue(OnboardingResource::canViewAny());
    }

    /**
     * The badge is what tells a bag holder someone is waiting on them, so it has
     * to count their own outstanding volunteers and nobody else's.
     */
    public function test_the_navigation_badge_counts_only_outstanding_volunteers(): void
    {
        $holder = $this->bagHolder();
        $this->pickerFor($holder);
        $this->pickerFor($holder)->forceFill(['onboarded_at' => now()])->save();
        $this->pickerFor($this->bagHolder());

        $this->actingAs($holder);

        $this->assertSame('1', OnboardingResource::getNavigationBadge());
    }

    /**
     * The holder is emailed on verification, not registration. Counting
     * unconfirmed sign-ups would claim more people are waiting than the holder
     * has been told about.
     */
    public function test_the_badge_ignores_volunteers_who_have_not_confirmed_their_email(): void
    {
        $holder = $this->bagHolder();

        $unconfirmed = User::factory()->picker()->unverified()->atPostcode('LS6 2AB')->create();
        $unconfirmed->forceFill(['assigned_bag_holder_id' => $holder->getKey()])->save();

        $this->actingAs($holder);

        $this->assertNull(OnboardingResource::getNavigationBadge());
    }

    public function test_unconfirmed_volunteers_are_still_listed_but_flagged(): void
    {
        $holder = $this->bagHolder();

        $unconfirmed = User::factory()->picker()->unverified()->atPostcode('LS6 2AB')->create();
        $unconfirmed->forceFill(['assigned_bag_holder_id' => $holder->getKey()])->save();

        Livewire::actingAs($holder)
            ->test(ListOnboardings::class)
            ->assertCanSeeTableRecords([$unconfirmed])
            ->assertSee('Not confirmed');
    }

    /**
     * A bag holder is very often a picker too. Without a strict scope they
     * would appear in their own queue and be invited to confirm they had
     * contacted themselves.
     */
    public function test_a_bag_holder_who_also_picks_is_not_in_their_own_queue(): void
    {
        $holder = User::factory()->bagHolder()->picker()->atPostcode('LS6 2AB')->create();
        $mine = $this->pickerFor($holder);

        Livewire::actingAs($holder)
            ->test(ListOnboardings::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$holder]);

        // The policy has to agree with the query, or the two drift apart.
        $this->assertFalse($holder->can('onboard', $holder));
    }

    /**
     * The resource filters to volunteers by query, so this guard on the policy
     * is what stops staff being onboardable by any other route.
     */
    public function test_staff_cannot_be_onboarded(): void
    {
        $organiser = User::factory()->organiser()->create();
        $holder = $this->bagHolder();

        $this->assertFalse($organiser->can('onboard', $holder));
    }

    public function test_the_badge_is_absent_when_nobody_is_waiting(): void
    {
        $this->actingAs($this->bagHolder());

        $this->assertNull(OnboardingResource::getNavigationBadge());
    }
}
