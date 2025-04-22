<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Restaurant;
use App\Models\Cheif;
use App\Models\Meal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => $this->faker->unique()->words(2, true),
            'description'   => $this->faker->sentence(),
            'rate'          => $this->faker->randomFloat(2, 3.5, 5),
            'delivery_time' => $this->faker->numberBetween(15, 60),
            'is_available'  => $this->faker->boolean(90),
        ];
    }
}
