<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Meal;
use App\Models\MealVariant;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderMeal>
 */
class OrderMealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      return [
        'order_id' => Order::factory()->create()->id, 
        'meal_id' => Meal::factory()->create()->id, 
        'meal_variant_id'=>MealVariant::factory()->create()->id, 
        'quantity' => $this->faker->numberBetween(1, 5), 
        'price' => $this->faker->randomFloat(2, 5, 100), 
    ];
    }
} 