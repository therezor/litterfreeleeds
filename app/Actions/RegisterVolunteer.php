<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

/**
 * Everything that happens when someone signs up at /join: create the account,
 * make them a Picker, match them to their nearest Purple Bag Holder, and start
 * email verification.
 *
 * The bag holder is deliberately NOT emailed here — that waits until the
 * address is verified, so holders are never sent chasing a typo. See
 * App\Listeners\NotifyBagHolderOfNewPicker.
 */
class RegisterVolunteer
{
    public function __construct(private readonly AssignNearestBagHolder $assign) {}

    /**
     * @param  array{name: string, email: string, password: string, postcode: string}  $data
     */
    public function execute(array $data): User
    {
        $picker = DB::transaction(function () use ($data): User {
            $picker = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
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
