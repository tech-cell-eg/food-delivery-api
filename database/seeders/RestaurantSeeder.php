<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\User;
use App\Models\Offer;
use App\Models\Order;
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
        $restaurants = [
            [
                'name' => 'Pasta Palace',
                'description' => 'Authentic Italian cuisine with homemade pasta and sauces',
                'delivery_fee' => 2.99,
                'delivery_time' => 30,
                'open_at' => '10:00',
                'close_at' => '22:00',
                'image' => 'restaurants/italian1.jpg',
                'categories' => ['Italian']
            ],
            [
                'name' => 'Taco Fiesta',
                'description' => 'Vibrant Mexican flavors with fresh ingredients and homemade tortillas',
                'delivery_fee' => 1.99,
                'delivery_time' => 20,
                'open_at' => '11:00',
                'close_at' => '23:00',
                'image' => 'restaurants/mexican1.jpg',
                'categories' => ['Mexican'],
            ],
            [
                'name' => 'Dragon Wok',
                'description' => 'Authentic Asian cuisine with a modern twist',
                'delivery_fee' => 3.50,
                'delivery_time' => 25,
                'open_at' => '10:30',
                'close_at' => '22:30',
                'image' => 'restaurants/asian1.jpg',
                'categories' => ['Asian'],

            ],
            [
                'name' => 'Burger Barn',
                'description' => 'Classic American burgers with hand-cut fries and milkshakes',
                'delivery_fee' => 2.50,
                'delivery_time' => 20,
                'open_at' => '11:00',
                'close_at' => '00:00',
                'image' => 'restaurants/american1.jpg',
                'categories' => ['American', 'Fast Food'],
            ],
            [
                'name' => 'Olive Tree',
                'description' => 'Mediterranean flavors with fresh ingredients and healthy options',
                'delivery_fee' => 2.99,
                'delivery_time' => 30,
                'open_at' => '09:00',
                'close_at' => '21:00',
                'image' => 'restaurants/mediterranean1.jpg',
                'categories' => ['Mediterranean', 'Vegetarian'],
            ],
            [
                'name' => 'Green Leaf',
                'description' => '100% plant-based meals with organic ingredients',
                'delivery_fee' => 0.00,
                'delivery_time' => 0,
                'open_at' => '08:00',
                'close_at' => '20:00',
                'image' => 'restaurants/vegan1.jpg',
                'categories' => ['Vegan', 'Vegetarian'],
            ],
            [
                'name' => 'Ocean Catch',
                'description' => 'Fresh seafood delivered daily from sustainable sources',
                'delivery_fee' => 4.50,
                'delivery_time' => 35,
                'open_at' => '12:00',
                'close_at' => '22:00',
                'image' => 'restaurants/seafood1.jpg',
                'categories' => ['Seafood'],

            ],
            [
                'name' => 'Sweet Heaven',
                'description' => 'Artisanal desserts and pastries made with love',
                'delivery_fee' => 1.99,
                'delivery_time' => 20,
                'open_at' => '08:00',
                'close_at' => '20:00',
                'image' => 'restaurants/desserts1.jpg',
                'categories' => ['Desserts'],
            ]
        ];

        foreach ($restaurants as $restaurantData) {
            $restaurant = Restaurant::create([
                'name' => $restaurantData['name'],
                'description' => $restaurantData['description'],
                'delivery_fee' => $restaurantData['delivery_fee'],
                'delivery_time' => $restaurantData['delivery_time'],
                'open_at' => $restaurantData['open_at'],
                'close_at' => $restaurantData['close_at'],
                'rate' => rand(0,5),
            ]);

            $restaurant->image()->create([
                'url' => $restaurantData['image'],
            ]);

            $categories = Category::whereIn('name', $restaurantData['categories'])->pluck('id');
            $restaurant->categories()->attach($categories);
        }

    }
}
