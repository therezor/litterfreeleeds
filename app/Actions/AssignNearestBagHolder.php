<?php

namespace App\Actions;

use App\Models\User;

/**
 * Matches a volunteer to the Purple Bag Holder nearest their postcode.
 *
 * An action rather than a scope because it is an operation with a side effect
 * — it persists the match — and it needs to be re-runnable from the panel when
 * a holder stands down. The query shape itself lives on the model, reusing the
 * same Haversine the public pick search uses.
 */
class AssignNearestBagHolder
{
    /**
     * Returns the holder that was assigned, or null when the volunteer has no
     * coordinates or no holder exists yet.
     */
    public function execute(User $picker): ?User
    {
        $holder = $this->nearestTo($picker);

        // forceFill: assigned_bag_holder_id is intentionally not mass-assignable.
        $picker->forceFill(['assigned_bag_holder_id' => $holder?->getKey()])->save();

        return $holder;
    }

    public function nearestTo(User $picker): ?User
    {
        if ($picker->latitude === null || $picker->longitude === null) {
            return null;
        }

        return User::query()
            ->purpleBagHolders()
            ->whereKeyNot($picker->getKey())
            ->withDistanceFrom((float) $picker->latitude, (float) $picker->longitude)
            // No distance cap: the requirement is to always assign the closest
            // holder, however far that is. Being far away is the bag holder's
            // signal to recruit locally, not a reason to leave someone matched
            // to nobody.
            ->reorder()
            ->orderBy('distance_miles')
            // Deterministic tie-break, so two holders at the same postcode do
            // not alternate between test runs.
            ->orderBy('users.id')
            ->first();
    }
}
