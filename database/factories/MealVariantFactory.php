<?php

namespace Database\Factories;

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
            'size'  => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 5, 50),
            'meal_id' => \App\Models\Meal::inRandomOrder()->first()->id,
        ];
    }
}
