<?php

namespace Tests\Feature;

use App\Filament\Resources\Onboardings\OnboardingResource;
use App\Models\Postcode;
use App\Models\User;
use App\Notifications\NewPickerAssigned;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    private function verificationUrl(User $user, ?string $hash = null): string
    {
        return URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id' => $user->getKey(),
            'hash' => $hash ?? sha1((string) $user->getEmailForVerification()),
        ]);
    }

    private function picker(): User
    {
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        return User::factory()->picker()->unverified()->atPostcode('LS6 2AB')->create();
    }

    /**
     * The whole reason verification is served publicly: Filament's own
     * verification routes sit behind the panel's Authenticate middleware, which
     * 403s anyone whose canAccessPanel() is false — every picker, by design.
     */
    public function test_a_picker_can_verify_without_a_session(): void
    {
        Notification::fake();
        $picker = $this->picker();

        // No actingAs — volunteers open the email on a different device.
        $this->get($this->verificationUrl($picker))
            ->assertRedirect(route('join.verified'));

        $this->assertNotNull($picker->fresh()->email_verified_at);
    }

    public function test_the_verified_page_renders(): void
    {
        Notification::fake();
        $picker = $this->picker();

        $this->get($this->verificationUrl($picker));

        $this->get(route('join.verified'))
            ->assertOk()
            ->assertSee('Email confirmed');
    }

    public function test_it_rejects_a_tampered_hash(): void
    {
        Notification::fake();
        $picker = $this->picker();

        $this->get($this->verificationUrl($picker, sha1('someone-else@example.com')))
            ->assertForbidden();

        $this->assertNull($picker->fresh()->email_verified_at);
    }

    public function test_it_rejects_an_unsigned_url(): void
    {
        Notification::fake();
        $picker = $this->picker();

        $this->get(route('verification.verify', [
            'id' => $picker->getKey(),
            'hash' => sha1((string) $picker->getEmailForVerification()),
        ]))->assertForbidden();
    }

    public function test_verifying_notifies_the_assigned_bag_holder(): void
    {
        Notification::fake();

        $picker = $this->picker();
        $holder = User::factory()->bagHolder()->atPostcode('LS6 2AB')->create();
        $picker->forceFill(['assigned_bag_holder_id' => $holder->getKey()])->save();

        $this->get($this->verificationUrl($picker));

        Notification::assertSentTo($holder, NewPickerAssigned::class);
    }

    /**
     * Bag holders hold no ViewAny:User, so a link to the Users list would land
     * the one email whose whole purpose is "go act on this" on a 403.
     */
    public function test_the_bag_holder_email_links_to_their_onboarding_list(): void
    {
        $picker = $this->picker();
        $holder = User::factory()->bagHolder()->atPostcode('LS6 2AB')->create();
        $picker->forceFill(['assigned_bag_holder_id' => $holder->getKey()])->save();

        $rendered = (new NewPickerAssigned($picker))->toMail($holder)->render();

        $this->assertStringContainsString(OnboardingResource::getUrl(), $rendered);
        $this->assertStringNotContainsString('/app/users', $rendered);
    }

    /**
     * Holders are told about a volunteer only once the address is known to be
     * real, so a typo never sends someone chasing a ghost.
     */
    public function test_the_bag_holder_is_not_notified_before_verification(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        $holder = User::factory()->bagHolder()->atPostcode('LS6 2AB')->create();

        $this->post('/join', [
            'name' => 'Alex Fletcher',
            'email' => 'alex@example.com',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'postcode' => 'LS6 2AB',
            'terms' => '1',
        ]);

        Notification::assertNotSentTo($holder, NewPickerAssigned::class);
    }

    public function test_verifying_twice_is_harmless(): void
    {
        Notification::fake();
        $picker = $this->picker();
        $url = $this->verificationUrl($picker);

        $this->get($url)->assertRedirect(route('join.verified'));
        $this->get($url)->assertRedirect(route('join.verified'));
    }

    public function test_a_verified_staff_member_lands_in_the_panel(): void
    {
        Notification::fake();
        $organiser = User::factory()->organiser()->unverified()->create();

        $this->get($this->verificationUrl($organiser))
            ->assertRedirect(url('/app'));
    }
}
