<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            'tomato',
            'lettuce',
            'cheese',
            'beef',
            'chicken',
            'onion',
            'pickles',
            'ketchup',
            'mayonnaise',
            'mushroom',
            'olives',
            'green Pepper',
            'garlic',
            'basil',
            'parmesan',
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate(['name' => $ingredient]);
        }
    }
}
