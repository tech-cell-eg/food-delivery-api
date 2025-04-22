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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'specialty' => $this->faker->word(),
            'experience' => $this->faker->numberBetween(1, 20),
            'user_id' => User::factory()->create()->id,
            
            //
        ];
    }
}
