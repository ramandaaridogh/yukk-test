<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $is_active = rand(1, 0);
        return [
            'username'      => fake()->unique()->userName(),
            'name'      => fake()->name(),
            'email'     => fake()->unique()->email(),
            'password'      => 'password',
            'pin' => null,
            'ammount_balance' => fake()->randomFloat(2, 0, 9999999),
            'birth_date'        => fake()->date(),
            'phone_number' => '08' . fake()->unique()->numberBetween(1000000000, 9999999999),
            'gender' => fake()->randomElement(Gender::class),
            'address' => fake()->address(),
            'image'     => '',
            'is_active'     => $is_active,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
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
