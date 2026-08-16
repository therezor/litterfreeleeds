<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Distance sorting for any model carrying denormalised latitude/longitude
 * columns copied off the postcodes table.
 *
 * Shared by CommunityPick (nearest pick to a visitor) and User (nearest Purple
 * Bag Holder to a new volunteer). The expression only depends on getTable(),
 * so there is nothing model-specific to parameterise.
 */
trait HasCoordinates
{
    /**
     * Adds a distance_miles column measured from the given point. Haversine over
     * a few dozen rows — no spatial extension, no bounding box. The clamp keeps
     * acos() from returning NaN when floating point overshoots 1 for a row
     * sitting on the exact point being searched from.
     *
     * Callers wanting distance to be the primary sort must reorder() first —
     * a scope such as upcoming() may already have applied an ordering.
     *
     * radians()/acos() are Postgres functions; this does not run on SQLite.
     */
    #[Scope]
    protected function withDistanceFrom(Builder $query, float $latitude, float $longitude): void
    {
        // selectRaw only appends, so without this the raw expression would be
        // the entire select list when no columns have been chosen yet.
        if (blank($query->getQuery()->columns)) {
            $query->select($this->getTable().'.*');
        }

        $query->selectRaw(
            '3959 * acos(least(1.0, greatest(-1.0,'
            .' cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))'
            .' + sin(radians(?)) * sin(radians(latitude))'
            .'))) as distance_miles',
            [$latitude, $longitude, $latitude]
        )->withCasts(['distance_miles' => 'float']);
    }
}
