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
                'address' => '123 Main St, Rome, Italy',
                'phone' => '+39 06 1234567',
                'email' => 'info@pastapalace.com',
                'delivery_fee' => 2.99,
                'open_at' => '10:00',
                'close_at' => '22:00',
                'average_delivery_time' => 30,
                'image' => 'restaurants/italian1.jpg',
                'categories' => ['Italian']
            ],
            [
                'name' => 'Taco Fiesta',
                'description' => 'Vibrant Mexican flavors with fresh ingredients and homemade tortillas',
                'address' => '456 Taco Lane, Mexico City',
                'phone' => '+52 55 98765432',
                'email' => 'hola@tacofiesta.com',
                'delivery_fee' => 1.99,
                'open_at' => '11:00',
                'close_at' => '23:00',
                'average_delivery_time' => 25,
                'image' => 'restaurants/mexican1.jpg',
                'categories' => ['Mexican']
            ],
            [
                'name' => 'Dragon Wok',
                'description' => 'Authentic Asian cuisine with a modern twist',
                'address' => '789 Bamboo Rd, Beijing',
                'phone' => '+86 10 87654321',
                'email' => 'contact@dragonwok.com',
                'delivery_fee' => 3.50,
                'open_at' => '10:30',
                'close_at' => '22:30',
                'average_delivery_time' => 35,
                'image' => 'restaurants/asian1.jpg',
                'categories' => ['Asian']
            ],
            [
                'name' => 'Burger Barn',
                'description' => 'Classic American burgers with hand-cut fries and milkshakes',
                'address' => '101 Burger Ave, New York',
                'phone' => '+1 212 555 1234',
                'email' => 'hello@burgerbarn.com',
                'delivery_fee' => 2.50,
                'open_at' => '11:00',
                'close_at' => '00:00',
                'average_delivery_time' => 20,
                'image' => 'restaurants/american1.jpg',
                'categories' => ['American', 'Fast Food']
            ],
            [
                'name' => 'Olive Tree',
                'description' => 'Mediterranean flavors with fresh ingredients and healthy options',
                'address' => '202 Olive St, Athens',
                'phone' => '+30 21 0987 6543',
                'email' => 'info@olivetree.com',
                'delivery_fee' => 2.99,
                'open_at' => '09:00',
                'close_at' => '21:00',
                'average_delivery_time' => 30,
                'image' => 'restaurants/mediterranean1.jpg',
                'categories' => ['Mediterranean', 'Vegetarian']
            ],
            [
                'name' => 'Green Leaf',
                'description' => '100% plant-based meals with organic ingredients',
                'address' => '303 Eco Blvd, California',
                'phone' => '+1 415 555 6789',
                'email' => 'hello@greenleaf.com',
                'delivery_fee' => 0.00,
                'open_at' => '08:00',
                'close_at' => '20:00',
                'average_delivery_time' => 25,
                'image' => 'restaurants/vegan1.jpg',
                'categories' => ['Vegan', 'Vegetarian']
            ],
            [
                'name' => 'Ocean Catch',
                'description' => 'Fresh seafood delivered daily from sustainable sources',
                'address' => '404 Harbor Dr, Sydney',
                'phone' => '+61 2 9876 5432',
                'email' => 'contact@oceancatch.com',
                'delivery_fee' => 4.50,
                'open_at' => '12:00',
                'close_at' => '22:00',
                'average_delivery_time' => 40,
                'image' => 'restaurants/seafood1.jpg',
                'categories' => ['Seafood']
            ],
            [
                'name' => 'Sweet Heaven',
                'description' => 'Artisanal desserts and pastries made with love',
                'address' => '505 Sugar Lane, Paris',
                'phone' => '+33 1 23 45 67 89',
                'email' => 'sweet@heaven.com',
                'delivery_fee' => 1.99,
                'open_at' => '08:00',
                'close_at' => '20:00',
                'average_delivery_time' => 15,
                'image' => 'restaurants/desserts1.jpg',
                'categories' => ['Desserts']
            ]
        ];

        foreach ($restaurants as $restaurantData) {
            $restaurant = Restaurant::create([
                'name' => $restaurantData['name'],
                'description' => $restaurantData['description'],
                'address' => $restaurantData['address'],
                'phone' => $restaurantData['phone'],
                'email' => $restaurantData['email'],
                'delivery_fee' => $restaurantData['delivery_fee'],
                'open_at' => $restaurantData['open_at'],
                'close_at' => $restaurantData['close_at'],
                'average_delivery_time' => $restaurantData['average_delivery_time'],
            ]);

            $restaurant->image()->create([
                'url' => $restaurantData['image'],
            ]);

            $categories = Category::whereIn('name', $restaurantData['categories'])->pluck('id');
            $restaurant->categories()->attach($categories);
        }

    }
}
