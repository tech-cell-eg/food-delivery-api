<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cheif;
use App\Models\Restaurant;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'cheif_id' => Cheif::inRandomOrder()->first()->id,
      'user_id' => User::inRandomOrder()->first()->id,
      'restaurant_id' => Restaurant::inRandomOrder()->first()->id, // Assuming you have 10 restaurants
      'rating' => $this->faker->numberBetween(1, 5),
      'comment' => $this->faker->sentence(),
      'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
    ];
  }
}
