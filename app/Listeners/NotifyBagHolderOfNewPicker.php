<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewPickerAssigned;
use Illuminate\Auth\Events\Verified;

/**
 * Auto-discovered from the handle() type-hint — no registration needed in
 * AppServiceProvider. Run `php artisan event:cache` on deploy.
 */
class NotifyBagHolderOfNewPicker
{
    public function handle(Verified $event): void
    {
        $picker = $event->user;

        if (! $picker instanceof User || ! $picker->hasRole(User::ROLE_PICKER)) {
            return;
        }

        $holder = $picker->assignedBagHolder;

        // No holder exists yet — RegisterVolunteer has already logged it and the
        // volunteer shows as "awaiting contact" in the panel.
        if (! $holder instanceof User) {
            return;
        }

        $holder->notify(new NewPickerAssigned($picker));
    }
}
