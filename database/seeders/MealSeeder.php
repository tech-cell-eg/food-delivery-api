<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\MealVariant;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $categories = Category::all();
        $ingredients = Ingredient::all();

        if ($restaurants->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No restaurants or categories found !!, Meal Seeding skipped.');
            return;
        }

        foreach ($restaurants as $restaurant) {
            Meal::factory()
                ->count(10)
                ->make()
                ->each(function ($meal) use ($restaurant, $categories, $ingredients) {
                    $meal->restaurant_id = $restaurant->id;
                    $meal->category_id = $categories->random()->id;
                    $meal->save();

                    $meal->image()->create([
                        'url' => 'images/meals/' . fake()->uuid . '.jpg',
                    ]);

                    $meal->ingredients()->attach(
                        $ingredients->random(rand(1, 3))->pluck('id')->toArray()
                    );

                    MealVariant::factory()
                        ->count(3)
                        ->create([
                            'meal_id' => $meal->id,
                        ]);
                });
        }
    }
}
