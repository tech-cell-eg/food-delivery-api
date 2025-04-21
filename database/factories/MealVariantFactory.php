<?php

namespace Database\Factories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealVariant>
 */
class MealVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'meal_id' => Meal::factory()->create()->id,
            'size' => $this->faker->randomElement(['small', 'medium', 'large']),
            'price' => $this->faker->randomFloat(2, 5, 100), // Random price between 5 and 100
        'is_available' => $this->faker->boolean(),

                 ];
    }
}
