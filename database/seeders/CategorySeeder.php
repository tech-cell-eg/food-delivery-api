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
            ['name' => 'Category 1', 'slug' => 'category-1'],
            ['name' => 'Category 2', 'slug' => 'category-2'],
            ['name' => 'Category 3', 'slug' => 'category-3'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
