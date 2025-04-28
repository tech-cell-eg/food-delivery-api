<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cheif>
 */
class CheifFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rate' => fake()->randomFloat(2, 3, 5),
            'description' => fake()->sentence(),
            'delivery_fee' => fake()->randomFloat(2, 3, 5),
            'delivery_time' => fake()->randomDigit(),
            'user_id' => User::factory()->create()->id,
        ];
    }
}
