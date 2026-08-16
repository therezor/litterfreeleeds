<?php

namespace App\Observers;

use App\Models\Postcode;
use App\Models\User;
use InvalidArgumentException;

class UserObserver
{
    /**
     * Copies coordinates off the postcodes table, mirroring
     * CommunityPickObserver — the nearest-bag-holder query then needs no join,
     * and a re-import never silently moves someone already matched.
     *
     * Two differences from the pick observer, both because users.postcode is
     * nullable while a pick's is required:
     *
     * 1. The dirty check comes first. Normalising unconditionally would turn a
     *    null postcode into '', marking the column dirty on every single save
     *    of every user who has no postcode — which is most of them.
     * 2. A blank postcode clears the derived columns instead of throwing. Only
     *    a postcode that was actually supplied and cannot be found is an error.
     */
    public function saving(User $user): void
    {
        if (! $user->isDirty('postcode')) {
            return;
        }

        if (blank($user->postcode)) {
            $user->forceFill([
                'postcode' => null,
                'outward_code' => null,
                'latitude' => null,
                'longitude' => null,
            ]);

            return;
        }

        $user->postcode = Postcode::normalise((string) $user->postcode);

        $postcode = Postcode::find($user->postcode);

        if (! $postcode instanceof Postcode) {
            throw new InvalidArgumentException(
                "Unknown postcode [{$user->postcode}]. Run `postcodes:import` before assigning one."
            );
        }

        $user->outward_code = $postcode->outward_code;
        $user->latitude = $postcode->latitude;
        $user->longitude = $postcode->longitude;
    }
}
