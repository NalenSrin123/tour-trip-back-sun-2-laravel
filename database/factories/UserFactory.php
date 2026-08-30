<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        'name' => 'User ' . rand(2, 100),
        'email' => 'user' . rand(2, 1000) . '@example.com',
        'email_verified_at' => now(),
        'password_hash' => static::$password ??= \Illuminate\Support\Facades\Hash::make('password'),
        'remember_token' => \Illuminate\Support\Str::random(10),
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
}
