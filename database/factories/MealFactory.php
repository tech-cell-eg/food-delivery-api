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
        'restaurant_id' => Restaurant::inRandomOrder()->first()->id,
         'cheif_id' => Cheif::inRandomOrder()->first()->id,
        'name' => $this->faker->word(), // اسم الوجبة عشوائي
        'description' => $this->faker->sentence(), // وصف عشوائي للوجبة
        'is_available' => $this->faker->boolean(), // هل الوجبة متوفرة أم لا (0 أو 1)
    ];
        
    }
}
