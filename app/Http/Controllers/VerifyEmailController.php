<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Email verification for volunteers, on the public site.
 *
 * Deliberately not Filament's: its verification routes sit behind the panel's
 * Authenticate middleware, which 403s anyone whose canAccessPanel() is false —
 * which is every Picker, by design. Routing verification through the panel
 * would leave volunteers permanently unable to verify.
 *
 * The route carries `signed` but NOT `auth`. Volunteers open the email on a
 * different device to the one they registered on, and this app has no route
 * named `login` for the guest middleware to redirect to — it is Filament's
 * `filament.app.auth.login` — so requiring a session would throw rather than
 * prompt. The signature is the authentication.
 *
 * Verifying does not sign anyone in. It hands a volunteer one capability —
 * choose a password — and SetPasswordController explains why that is the limit.
 */
class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // hash_equals, not ===: this is compared against a value from the URL.
        if (! hash_equals($hash, sha1((string) $user->getEmailForVerification()))) {
            throw new AccessDeniedHttpException('Invalid verification link.');
        }

        // Only the verification itself is conditional. A volunteer who follows
        // a second valid link still gets the password step below — the link is
        // short-lived proof they can read the inbox, which is the same thing a
        // password reset asks for.
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            // Fires NotifyBagHolderOfNewPicker — the bag holder hears about a
            // volunteer only once the address is known to be real.
            event(new Verified($user));
        }

        return $this->destinationFor($request, $user);
    }

    /**
     * Volunteers go on to choose a password; staff already have one.
     *
     * Staff can reach this route because Laravel's stock VerifyEmail
     * notification resolves the `verification.verify` name we now own. Sending
     * them through a volunteer signup step would be baffling, so they go to
     * the panel instead.
     *
     * Branching on the role rather than canAccessPanel(): volunteers can reach
     * the panel now, but they are the ones with a placeholder password and a
     * bag holder to hear from.
     */
    protected function destinationFor(Request $request, User $user): RedirectResponse
    {
        if (! $user->hasRole(User::ROLE_PICKER)) {
            return redirect(Filament::getPanel('app')->getUrl());
        }

        SetPasswordController::grantTo($request, $user);

        return to_route('join.password');
    }
}
