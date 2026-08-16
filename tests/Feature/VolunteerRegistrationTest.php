<?php

namespace Tests\Feature;

use App\Models\Postcode;
use App\Models\User;
use App\Notifications\WelcomeVolunteer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VolunteerRegistrationTest extends TestCase
{
    /**
     * @return array{name: string, email: string, postcode: string, terms: string}
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Alex Fletcher',
            'email' => 'alex@example.com',
            'postcode' => 'LS6 2AB',
            'terms' => '1',
        ], $overrides);
    }

    public function test_the_join_page_renders(): void
    {
        $this->get('/join')
            ->assertOk()
            ->assertSee('Join the pickers')
            ->assertSee('Your postcode');
    }

    public function test_it_registers_a_volunteer_as_a_picker(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        $this->post('/join', $this->validPayload())
            ->assertRedirect(route('join.welcome'));

        $volunteer = User::query()->where('email', 'alex@example.com')->sole();

        $this->assertTrue($volunteer->hasRole(User::ROLE_PICKER));
        $this->assertNotNull($volunteer->terms_accepted_at);
        $this->assertNull($volunteer->onboarded_at);
        $this->assertNull($volunteer->email_verified_at);
    }

    public function test_it_denormalises_the_postcode(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        // Typed with lowercase and no space, as a person would.
        $this->post('/join', $this->validPayload(['postcode' => 'ls62ab']));

        $volunteer = User::query()->where('email', 'alex@example.com')->sole();

        $this->assertSame('LS62AB', $volunteer->postcode);
        $this->assertSame('LS6', $volunteer->outward_code);
        $this->assertSame(53.8155, $volunteer->latitude);
        $this->assertSame(-1.5680, $volunteer->longitude);
    }

    public function test_the_welcome_page_shows_the_conditions_of_use(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $this->post('/join', $this->validPayload());

        $this->get(route('join.welcome'))
            ->assertOk()
            ->assertSee('Welcome, Alex Fletcher')
            ->assertSee('Check your inbox')
            ->assertSee('Purple Bag Holder')
            ->assertSee('Safe working rules')
            ->assertSee('purplebags@leeds.gov.uk');
    }

    public function test_the_welcome_page_is_not_reachable_without_registering(): void
    {
        $this->get(route('join.welcome'))->assertRedirect(route('join.create'));
    }

    public function test_it_sends_the_welcome_email(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $this->post('/join', $this->validPayload());

        $volunteer = User::query()->where('email', 'alex@example.com')->sole();

        Notification::assertSentTo($volunteer, WelcomeVolunteer::class);
    }

    /**
     * The form has no password field at all — a volunteer gives us three
     * details and none of them is a credential.
     */
    public function test_the_join_form_does_not_ask_for_a_password(): void
    {
        $this->get('/join')
            ->assertOk()
            ->assertDontSee('name="password"', false)
            ->assertDontSee('name="password_confirmation"', false);
    }

    /**
     * The column is NOT NULL, so a new account holds a placeholder. It must be
     * a real hash of something nobody knows — the failure to guard against is a
     * fixed or empty string that would let anyone sign in as any volunteer who
     * has not reached the password step yet.
     */
    public function test_a_new_account_cannot_be_signed_in_to(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $this->post('/join', $this->validPayload());
        $this->post('/join', $this->validPayload(['email' => 'sam@example.com']));

        $volunteers = User::query()->get();

        $this->assertCount(2, $volunteers);

        foreach ($volunteers as $volunteer) {
            $this->assertTrue(Hash::isHashed((string) $volunteer->password));
            $this->assertFalse(Hash::check('', (string) $volunteer->password));
            $this->assertFalse(Hash::check('password', (string) $volunteer->password));
        }

        // Two placeholders from the same run must not collide.
        $this->assertNotSame($volunteers[0]->password, $volunteers[1]->password);
    }

    /**
     * The whole point of moving the password to its own step: nothing that
     * could sign in as this volunteer is ever put in an inbox.
     */
    public function test_the_welcome_email_carries_no_credentials(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $this->post('/join', $this->validPayload());

        $volunteer = User::query()->where('email', 'alex@example.com')->sole();

        $body = (string) Notification::sent($volunteer, WelcomeVolunteer::class)
            ->sole()
            ->toMail($volunteer)
            ->render();

        // The structural marker, not the bare word "password" — the email talks
        // about passwords on purpose, it just never carries one.
        $this->assertStringNotContainsString('Your sign-in details', $body);
        $this->assertStringContainsString('Confirm my email address', $body);
        $this->assertStringContainsString('choose your password', $body);
    }

    public function test_the_consent_box_starts_unticked(): void
    {
        $this->get('/join')
            ->assertOk()
            ->assertSee('name="terms"', false)
            ->assertDontSee('checked', false);
    }

    /**
     * Every other field repopulates after a validation failure. Consent does
     * not — it has to be given afresh rather than arriving pre-ticked.
     */
    public function test_consent_is_not_repopulated_after_a_validation_failure(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $this->from('/join')
            ->post('/join', $this->validPayload(['email' => 'not-an-email']))
            ->assertRedirect('/join');

        $response = $this->get('/join');

        // The name they typed comes back; the tick does not.
        $response->assertSee('value="Alex Fletcher"', false);
        $response->assertDontSee('checked', false);
    }

    public function test_it_requires_the_terms_to_be_accepted(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $this->post('/join', $this->validPayload(['terms' => null]))
            ->assertSessionHasErrors('terms');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_it_rejects_an_unknown_postcode(): void
    {
        // Well formed, but not in the imported directory.
        $this->post('/join', $this->validPayload(['postcode' => 'LS99 9ZZ']))
            ->assertSessionHasErrors('postcode');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_it_rejects_something_that_is_not_a_postcode(): void
    {
        $this->post('/join', $this->validPayload(['postcode' => 'banana']))
            ->assertSessionHasErrors('postcode');
    }

    public function test_it_rejects_a_duplicate_email(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        User::factory()->create(['email' => 'alex@example.com']);

        $this->post('/join', $this->validPayload())
            ->assertSessionHasErrors('email');
    }

    /**
     * /join is an open public POST, so the fields that decide who looks after a
     * volunteer and whether they are onboarded must not be settable from it.
     */
    public function test_it_ignores_mass_assignment_of_onboarding_fields(): void
    {
        Notification::fake();
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $stranger = User::factory()->create();

        $this->post('/join', $this->validPayload([
            'assigned_bag_holder_id' => $stranger->getKey(),
            'onboarded_at' => now()->toDateTimeString(),
            'terms_accepted_at' => '2020-01-01 00:00:00',
        ]));

        $volunteer = User::query()->where('email', 'alex@example.com')->sole();

        $this->assertNull($volunteer->assigned_bag_holder_id);
        $this->assertNull($volunteer->onboarded_at);
        $this->assertNotNull($volunteer->terms_accepted_at);
        $this->assertTrue($volunteer->terms_accepted_at->isToday());
    }

    public function test_the_old_panel_registration_url_redirects_to_join(): void
    {
        $this->get('/app/register')->assertRedirect('/join');
    }
}
