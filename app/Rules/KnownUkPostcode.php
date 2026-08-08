<?php

namespace App\Rules;

use App\Models\Postcode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A postcode we can actually place on a map — it must be well formed *and*
 * present in the imported ONS Postcode Directory, because every pick's
 * coordinates are read straight out of that table.
 */
class KnownUkPostcode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalised = Postcode::normalise(is_string($value) ? $value : null);

        if (! Postcode::isWellFormed($normalised)) {
            $fail('The :attribute must be a valid UK postcode, for example LS1 1UR.');

            return;
        }

        if (! Postcode::whereKey($normalised)->exists()) {
            $fail('We could not find :attribute in the UK postcode directory. Check it is correct, or ask an administrator to refresh the postcode data.');
        }
    }
}
