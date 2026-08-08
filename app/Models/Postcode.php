<?php

namespace App\Models;

use Database\Factories\PostcodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A UK postcode and its centroid, imported from the ONS Postcode Directory.
 * Reference data — managed by the postcodes:import command, never by a user.
 */
#[Fillable(['postcode', 'outward_code', 'latitude', 'longitude'])]
class Postcode extends Model
{
    /** @use HasFactory<PostcodeFactory> */
    use HasFactory;

    protected $primaryKey = 'postcode';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Strip everything that is not a letter or digit, then uppercase — the
     * storage and lookup form. "ls1 1ur" and "LS1-1UR" both become "LS11UR".
     */
    public static function normalise(?string $postcode): string
    {
        return Str::of((string) $postcode)
            ->replaceMatches('/[^A-Za-z0-9]/', '')
            ->upper()
            ->toString();
    }

    /**
     * Checks the shape only — whether it is a postcode that actually exists is
     * a lookup, not a regex. Runs against the normalised form so it never has
     * to reason about spacing.
     */
    public static function isWellFormed(?string $postcode): bool
    {
        $normalised = static::normalise($postcode);

        return preg_match('/^(GIR0AA|[A-Z]{1,2}[0-9][A-Z0-9]?[0-9][A-Z]{2})$/', $normalised) === 1;
    }

    /**
     * The human form, with the space before the final three characters.
     */
    public static function format(?string $postcode): string
    {
        $normalised = static::normalise($postcode);

        if (Str::length($normalised) < 5) {
            return $normalised;
        }

        return Str::substr($normalised, 0, -3).' '.Str::substr($normalised, -3);
    }

    /**
     * The outward code — everything before the space, e.g. "LS1" of "LS1 1UR".
     * The inward code is always exactly three characters.
     */
    public static function outwardCodeFor(?string $postcode): string
    {
        return Str::substr(static::normalise($postcode), 0, -3);
    }
}
