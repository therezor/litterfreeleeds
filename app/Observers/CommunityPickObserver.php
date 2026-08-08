<?php

namespace App\Observers;

use App\Models\CommunityPick;
use App\Models\Postcode;
use InvalidArgumentException;

class CommunityPickObserver
{
    public function creating(CommunityPick $pick): void
    {
        $pick->slug ??= CommunityPick::uniqueSlug($pick->name, $pick->date);
    }

    /**
     * Coordinates and the outward code are copied from the postcodes table so
     * the public distance query needs no join, and so a re-import never moves a
     * pick that has already been advertised.
     *
     * The ward is deliberately NOT copied — it is read through the postcode, so
     * it is always current and postcodes:import stays purely reference data.
     */
    public function saving(CommunityPick $pick): void
    {
        $pick->postcode = Postcode::normalise((string) $pick->postcode);

        if (! $pick->isDirty('postcode')) {
            return;
        }

        $postcode = Postcode::find($pick->postcode);

        if (! $postcode instanceof Postcode) {
            throw new InvalidArgumentException(
                "Unknown postcode [{$pick->postcode}]. Run `postcodes:import` before creating picks."
            );
        }

        $pick->outward_code = $postcode->outward_code;
        $pick->latitude = $postcode->latitude;
        $pick->longitude = $postcode->longitude;
    }
}
