<?php

namespace Tests\Feature;

use App\Models\Postcode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SetPasswordTest extends TestCase
{
    private function picker(): User
    {
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        return User::factory()->picker()->unverified()->atPostcode('LS6 2AB')->create();
    }

    private function verificationUrl(User $user): string
    {
        return URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id' => $user->getKey(),
            'hash' => sha1((string) $user->getEmailForVerification()),
        ]);
    }

    /**
     * Following the link is what earns the right to set a password.
     */
    private function arriveFromTheEmail(User $picker): void
    {
        $this->get($this->verificationUrl($picker))->assertRedirect(route('join.password'));
    }

    public function test_the_verification_link_leads_to_the_password_step(): void
    {
        Notification::fake();
        $picker = $this->picker();

        $this->arriveFromTheEmail($picker);

        $this->get(route('join.password'))
            ->assertOk()
            ->assertSee('Choose a password')
            ->assertSee($picker->name);
    }

    public function test_a_volunteer_can_set_their_password_and_is_signed_in(): void
    {
        Notification::fake();
        $picker = $this->picker();
        $this->arriveFromTheEmail($picker);

        $this->post(route('join.password.store'), [
            'password' => 'hedgerow-thistle-9',
            'password_confirmation' => 'hedgerow-thistle-9',
        ])->assertRedirect(route('join.verified'));

        $this->assertTrue(Hash::check('hedgerow-thistle-9', (string) $picker->fresh()->password));
        $this->assertAuthenticatedAs($picker);
    }

    /**
     * The signed link proves someone can read the inbox — nothing more. It must
     * not, on its own, produce a signed-in session: verification links live for
     * an hour and get forwarded, and every role can reach the panel.
     */
    public function test_verifying_alone_does_not_sign_anyone_in(): void
    {
        Notification::fake();
        $picker = $this->picker();

        $this->arriveFromTheEmail($picker);

        $this->assertGuest();
    }

    /**
     * The real security boundary. A POST can arrive without ever rendering the
     * form, so the capability is re-checked here rather than assumed.
     */
    public function test_the_password_cannot_be_set_without_following_the_link(): void
    {
        $picker = $this->picker();
        $original = $picker->password;

        $this->post(route('join.password.store'), [
            'password' => 'hedgerow-thistle-9',
            'password_confirmation' => 'hedgerow-thistle-9',
        ])->assertRedirect(route('join.create'));

        $this->assertSame($original, $picker->fresh()->password);
        $this->assertGuest();
    }

    public function test_the_password_page_redirects_without_the_capability(): void
    {
        $this->get(route('join.password'))->assertRedirect(route('join.create'));
    }

    /**
     * Setting a password spends the capability. Otherwise the session would
     * stay able to overwrite the password indefinitely.
     */
    public function test_the_capability_is_spent_once_used(): void
    {
        Notification::fake();
        $picker = $this->picker();
        $this->arriveFromTheEmail($picker);

        $this->post(route('join.password.store'), [
            'password' => 'hedgerow-thistle-9',
            'password_confirmation' => 'hedgerow-thistle-9',
        ]);

        $this->post(route('join.password.store'), [
            'password' => 'somebody-elses-choice-4',
            'password_confirmation' => 'somebody-elses-choice-4',
        ])->assertRedirect(route('join.create'));

        $this->assertTrue(Hash::check('hedgerow-thistle-9', (string) $picker->fresh()->password));
    }

    public function test_it_rejects_a_mismatched_confirmation(): void
    {
        Notification::fake();
        $picker = $this->picker();
        $this->arriveFromTheEmail($picker);

        $this->post(route('join.password.store'), [
            'password' => 'hedgerow-thistle-9',
            'password_confirmation' => 'something-else',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_it_rejects_a_password_that_is_too_short(): void
    {
        Notification::fake();
        $picker = $this->picker();
        $this->arriveFromTheEmail($picker);

        $this->post(route('join.password.store'), [
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    /**
     * Abandoning the step is a supported outcome, not a broken account — the
     * address is verified and the bag holder has been told, so the only thing
     * missing is a password.
     */
    public function test_abandoning_the_step_still_leaves_a_verified_volunteer(): void
    {
        Notification::fake();
        $picker = $this->picker();

        $this->arriveFromTheEmail($picker);

        $this->assertNotNull($picker->fresh()->email_verified_at);
    }

    /**
     * Both the welcome email and the password page tell volunteers that
     * "forgotten password" will get them in if they skip this step or the link
     * expires. That promise rests on the panel's reset pages being guest routes
     * rather than panel-gated ones — which is exactly the trap that made
     * verification itself move off the panel in the first place.
     */
    public function test_a_volunteer_who_skips_it_can_still_get_in_by_resetting(): void
    {
        Notification::fake();
        $picker = $this->picker();
        $this->arriveFromTheEmail($picker);

        // Reachable with no session and no password — a picker is not 403'd.
        $this->get(route('filament.app.auth.password-reset.request'))->assertOk();

        $status = PasswordBroker::broker()->reset(
            [
                'email' => $picker->email,
                'password' => 'hedgerow-thistle-9',
                'password_confirmation' => 'hedgerow-thistle-9',
                'token' => PasswordBroker::broker()->createToken($picker),
            ],
            fn (User $user, string $password) => $user->forceFill(['password' => $password])->save(),
        );

        $this->assertSame(PasswordBroker::PASSWORD_RESET, $status);

        // And the reset password is one they can actually sign in with.
        $this->assertTrue(Auth::attempt([
            'email' => $picker->email,
            'password' => 'hedgerow-thistle-9',
        ]));
    }
}
