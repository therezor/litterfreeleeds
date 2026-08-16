<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Everything that happens when someone signs up at /join: create the account,
 * make them a Picker, match them to their nearest Purple Bag Holder, and start
 * email verification.
 *
 * The form asks for three things and none of them is a password. Choosing one
 * was the longest part of the form and the part people abandoned it on, so it
 * has moved to its own step, after the volunteer clicks the link in the welcome
 * email — see App\Http\Controllers\SetPasswordController. Until then the
 * account exists but cannot be signed in to.
 *
 * The bag holder is deliberately NOT emailed here — that waits until the
 * address is verified, so holders are never sent chasing a typo. See
 * App\Listeners\NotifyBagHolderOfNewPicker.
 */
class RegisterVolunteer
{
    public function __construct(private readonly AssignNearestBagHolder $assign) {}

    /**
     * @param  array{name: string, email: string, postcode: string}  $data
     */
    public function execute(array $data): User
    {
        $picker = DB::transaction(function () use ($data): User {
            $picker = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                // A placeholder, not a password: the column is NOT NULL and the
                // volunteer has not chosen one yet. Random, never shown to
                // anyone and never written down, so the account is unusable
                // until they set their own — which is the intended state, not a
                // gap. Anyone who abandons the flow can still get in through the
                // panel's password reset.
                'password' => Str::random(64),
                // The observer resolves this to outward_code/latitude/longitude.
                'postcode' => $data['postcode'],
            ]);

            // Not mass-assignable, by design — see the note on User's #[Fillable].
            $picker->forceFill(['terms_accepted_at' => now()])->save();

            // findOrCreate, not assignRole by name: the Picker role carries no
            // permissions, so creating it on demand is harmless — and it means
            // a forgotten VolunteerRolesSeeder cannot take public registration
            // down with a RoleDoesNotExist.
            $picker->assignRole(Role::findOrCreate(User::ROLE_PICKER, 'web'));

            // Must happen after assignRole: the match filters on the role, and
            // a picker should never be matched to themselves.
            $this->assign->execute($picker);

            return $picker;
        });

        if ($picker->assigned_bag_holder_id === null) {
            // Not an error — on day one there are no bag holders at all. The
            // panel shows these as "awaiting contact" so they are actionable.
            Log::warning('Volunteer registered with no Purple Bag Holder to match.', [
                'user_id' => $picker->getKey(),
                'postcode' => $picker->postcode,
            ]);
        }

        // Illuminate's Registered, not Filament's — only this one is wired to
        // SendEmailVerificationNotification, which calls our overridden
        // User::sendEmailVerificationNotification() and sends WelcomeVolunteer.
        event(new Registered($picker));

        return $picker;
    }
}
