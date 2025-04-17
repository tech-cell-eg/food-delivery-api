<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Review;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $category = Category::firstOrCreate([
            'slug' => 'italian',
        ], [
            'name' => 'Italian',
        ]);

        $restaurant = Restaurant::create([
            'name' => 'Pasta Palace',
            'slug' => 'pasta-palace',
            'description' => 'Authentic Italian cuisine',
            'address' => '123 Italy St',
            'phone' => '123-456-7890',
            'email' => 'info@pastapalace.com',
            'delivery_fee' => 5.00,
            'open_at' => '10:00',
            'close_at' => '22:00',
        ]);

        $restaurant->categories()->attach($category);

        Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'rating' => 4,
            'comment' => 'Great food, fast delivery!',
        ]);

        Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'rating' => 5,
            'comment' => 'Amazing pasta!',
        ]);
    }
}
