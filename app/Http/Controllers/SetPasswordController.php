<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The last step of joining: choosing a password, once the email address has
 * been confirmed.
 *
 * It sits here rather than on the /join form because a password was the longest
 * field on a form whose other three questions are name, email and postcode —
 * and because moving it here means no credential is ever generated for a
 * volunteer or sent to their inbox. Until they arrive, the account holds a
 * random placeholder nobody has seen (see RegisterVolunteer).
 *
 * Reaching this page is a capability granted by VerifyEmailController when a
 * volunteer follows a valid signed link, and held in the session. Deliberately
 * a capability rather than a login: a verification link is valid for an hour
 * and gets forwarded, and signing someone in from one would hand over the whole
 * account — including the panel, which every role can reach — when all the link
 * proves is that they can read the inbox. The login happens below instead, once
 * a password has actually been chosen.
 */
class SetPasswordController extends Controller
{
    /**
     * Holds the id of the volunteer allowed to set a password right now.
     * Private on purpose — the key and the protocol around it are this class's
     * business, and grantTo() is the only way in.
     */
    private const SESSION_KEY = 'join.set_password_for';

    /**
     * Let this session set $volunteer's password. Called by
     * VerifyEmailController once a signed link checks out.
     *
     * It lives here rather than there so the protocol — regenerate first, then
     * write — is in one place and cannot be half-remembered by a later caller.
     * The regenerate matters because this key becomes an authenticated session
     * in store() below: without it, a session id planted in the browser
     * beforehand would be the one that ends up signed in.
     */
    public static function grantTo(Request $request, User $volunteer): void
    {
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, $volunteer->getKey());
    }

    public function create(Request $request): View|RedirectResponse
    {
        $volunteer = $this->volunteerFor($request);

        if (! $volunteer instanceof User) {
            return to_route('join.create');
        }

        return view('pages.join-password', ['name' => $volunteer->name]);
    }

    public function store(SetPasswordRequest $request): RedirectResponse
    {
        // Re-read rather than trusting the page that rendered the form: this is
        // the actual boundary, and a POST can arrive without a GET.
        $volunteer = $this->volunteerFor($request);

        if (! $volunteer instanceof User) {
            return to_route('join.create');
        }

        // The 'hashed' cast does the hashing on write.
        $volunteer->update(['password' => (string) $request->validated('password')]);

        // Spend the capability before using it. Auth::login() migrates the
        // session id on the way in, so the id that carried an anonymous
        // capability is not the id that now carries a signed-in volunteer.
        $request->session()->forget(self::SESSION_KEY);

        Auth::login($volunteer);

        return to_route('join.verified')->with('passwordSet', true);
    }

    /**
     * The volunteer this session is allowed to act for, or null.
     */
    protected function volunteerFor(Request $request): ?User
    {
        $id = $request->session()->get(self::SESSION_KEY);

        if ($id === null) {
            return null;
        }

        return User::query()->whereKey($id)->first();
    }
}
