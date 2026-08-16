<?php

namespace Database\Factories;

use App\Models\Postcode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Postcode is deliberately absent from definition() — most users have none,
     * and defaulting it would make every factory call create a postcode row.
     */
    public function atPostcode(Postcode|string $postcode): static
    {
        return $this->state(fn (): array => [
            'postcode' => $postcode instanceof Postcode ? $postcode->postcode : $postcode,
        ]);
    }

    public function picker(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(
            Role::findOrCreate(User::ROLE_PICKER, 'web')
        ));
    }

    public function bagHolder(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(
            Role::findOrCreate(User::ROLE_BAG_HOLDER, 'web')
        ));
    }

    public function organiser(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(
            Role::findOrCreate(User::ROLE_ORGANISER, 'web')
        ));
    }

    public function onboarded(): static
    {
        return $this->state(fn (): array => ['onboarded_at' => now()]);
    }
}
