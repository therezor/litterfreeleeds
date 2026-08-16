<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

/**
 * NOTE: `shield:generate` regenerates this file from config/filament-shield.php
 * (policies.generate is true). The record parameters and onboard() below are
 * hand-written — re-apply them if you ever regenerate.
 */
class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('View:User') && $this->isVisibleTo($authUser, $user);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Update:User') && $this->isVisibleTo($authUser, $user);
    }

    public function delete(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Delete:User') && $this->isVisibleTo($authUser, $user);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:User');
    }

    /**
     * Confirming a volunteer has been contacted and given bags. Its own
     * permission rather than a use of Update:User, because UserForm exposes the
     * roles multi-select and the password field — a bag holder holding
     * Update:User could make themselves Super Admin. This only stamps
     * onboarded_at.
     */
    public function onboard(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Onboard:User')
            // Onboarding is a volunteer's journey. Offering it against a bag
            // holder or an admin — including the viewer's own row — is an
            // action that would mean nothing if taken.
            && $user->hasRole(User::ROLE_PICKER)
            && ! $user->hasBeenOnboarded()
            && $this->isVisibleTo($authUser, $user);
    }

    /**
     * Scoping the list query stops records leaking into the table; this stops a
     * hand-typed /app/users/7/edit reaching someone else's volunteer.
     *
     * Must stay in step with User::applyVisibility(), which asks the same
     * question of a query. In particular there is deliberately no "or it is
     * you" clause: a bag holder who also picks would otherwise be able to
     * onboard themselves.
     */
    protected function isVisibleTo(AuthUser $authUser, User $user): bool
    {
        if (! $authUser instanceof User) {
            return false;
        }

        return $authUser->seesAllVolunteers()
            || $user->assigned_bag_holder_id === $authUser->getKey();
    }
}
