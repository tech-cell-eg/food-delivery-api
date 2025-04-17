<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            $availableUsers = $users->random(min(3, $users->count()));

            foreach ($availableUsers as $user) {
                Review::factory()->create([
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurant->id,
                ]);
            }
        }
    }
}
