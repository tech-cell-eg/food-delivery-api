<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Italian',
                'image' => 'categories/italian.jpg'
            ],
            [
                'name' => 'Mexican',
                'image' => 'categories/mexican.jpg'
            ],
            [
                'name' => 'Asian',
                'image' => 'categories/asian.jpg'
            ],
            [
                'name' => 'American',
                'image' => 'categories/american.jpg'
            ],
            [
                'name' => 'Mediterranean',
                'image' => 'categories/mediterranean.jpg'
            ],
            [
                'name' => 'Vegetarian',
                'image' => 'categories/vegetarian.jpg'
            ],
            [
                'name' => 'Vegan',
                'image' => 'categories/vegan.jpg'
            ],
            [
                'name' => 'Fast Food',
                'image' => 'categories/fast-food.jpg'
            ],
            [
                'name' => 'Seafood',
                'image' => 'categories/seafood.jpg'
            ],
            [
                'name' => 'Desserts',
                'image' => 'categories/desserts.jpg'
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
            ]);

            $category->image()->create([
                'url' => $categoryData['image'],
            ]);
        }

    }
}
