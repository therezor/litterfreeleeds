<?php

namespace App\Http\Controllers;

use App\Actions\RegisterVolunteer;
use App\Http\Requests\JoinRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JoinController extends Controller
{
    public function __construct(private readonly RegisterVolunteer $register) {}

    public function create(): View
    {
        return view('pages.join');
    }

    public function store(JoinRequest $request): RedirectResponse
    {
        /** @var array{name: string, email: string, postcode: string} $data */
        $data = $request->safe()->only(['name', 'email', 'postcode']);

        $volunteer = $this->register->execute($data);

        // Post/redirect/get, with the outcome flashed rather than in the query
        // string: whether someone has a bag holder yet is their business, and
        // the welcome URL gets pasted into group chats.
        return to_route('join.welcome')->with('registered', [
            'name' => $volunteer->name,
            'hasBagHolder' => $volunteer->assigned_bag_holder_id !== null,
        ]);
    }

    /**
     * The final step of registration: what happens next, plus the conditions
     * of use. Only reachable straight after registering — otherwise it is a
     * confirmation of nothing.
     */
    public function welcome(Request $request): View|RedirectResponse
    {
        /** @var array{name: string, hasBagHolder: bool}|null $registered */
        $registered = $request->session()->get('registered');

        if ($registered === null) {
            return to_route('join.create');
        }

        return view('pages.join-welcome', $registered);
    }
}
