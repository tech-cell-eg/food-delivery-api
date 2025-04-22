<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id, // Assuming you have 100 users
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),
            'restaurant_id' => $this->faker->numberBetween(1, 10), // Assuming you have 10 restaurants
            'delivery_fee' => $this->faker->randomFloat(2, 0, 20), // Random delivery fee between 0 and 20
            'discount_amount' => $this->faker->randomFloat(2, 0, 50),
            
            // Random discount amount between 0 and 50
            'subtotal' => $this->faker->randomFloat(2, 10, 100), // Random subtotal between 10 and 100
        'total_amount'=>$this->faker->randomFloat(2, 10, 100), // Random total amount between 10 and 100
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),

            //
        ];
    }
}
