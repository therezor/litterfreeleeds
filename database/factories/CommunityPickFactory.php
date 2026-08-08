<?php

namespace Database\Factories;

use App\Models\CommunityPick;
use App\Models\Postcode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunityPick>
 */
class CommunityPickFactory extends Factory
{
    protected $model = CommunityPick::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Roundhay Park Pick',
                'Hyde Park Clean-Up',
                'Meanwood Beck Tidy',
                'Kirkstall Abbey Litter Pick',
                'Woodhouse Moor Morning Pick',
            ]).' '.fake()->unique()->numberBetween(1, 100_000),
            'date' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'time_from' => '10:00',
            'time_to' => '12:00',
            'excerpt' => fake()->sentence(10),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->streetName().' car park',

            // Lazily resolved: Laravel merges state over the definition before
            // expanding closures, so atPostcode() replaces this outright rather
            // than leaving an orphaned postcode row behind.
            'postcode' => fn (): string => Postcode::factory()->create()->postcode,

            'responsible_user_id' => User::factory(),
        ];
    }

    public function past(): static
    {
        return $this->state(fn (): array => [
            'date' => fake()->dateTimeBetween('-6 months', '-1 week')->format('Y-m-d'),
        ]);
    }

    public function on(string $date): static
    {
        return $this->state(fn (): array => ['date' => $date]);
    }

    public function atPostcode(Postcode|string $postcode): static
    {
        return $this->state(fn (): array => [
            'postcode' => $postcode instanceof Postcode ? $postcode->postcode : $postcode,
        ]);
    }
}
