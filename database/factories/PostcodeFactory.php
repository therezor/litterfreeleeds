<?php

namespace Database\Factories;

use App\Models\Postcode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Postcode>
 */
class PostcodeFactory extends Factory
{
    protected $model = Postcode::class;

    public function definition(): array
    {
        $outwardCode = fake()->randomElement(['LS1', 'LS2', 'LS6', 'LS7', 'LS8', 'LS11', 'LS15']);

        return [
            'postcode' => $outwardCode.fake()->numberBetween(1, 9).Str::upper(fake()->lexify('??')),
            'outward_code' => $outwardCode,
            'latitude' => fake()->randomFloat(6, 53.75, 53.90),
            'longitude' => fake()->randomFloat(6, -1.70, -1.40),
        ];
    }

    /**
     * Pin the centroid, so distance assertions are deterministic.
     */
    public function at(float $latitude, float $longitude): static
    {
        return $this->state(fn (): array => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function withPostcode(string $postcode): static
    {
        return $this->state(fn (): array => [
            'postcode' => Postcode::normalise($postcode),
            'outward_code' => Postcode::outwardCodeFor($postcode),
        ]);
    }
}
