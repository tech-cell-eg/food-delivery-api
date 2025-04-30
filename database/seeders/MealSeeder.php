<?php

namespace Database\Seeders;

use App\Models\{Restaurant, Category, Ingredient, Meal, MealVariant, Cheif};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();

        if ($restaurants->isEmpty()) {
            $this->command->warn('No restaurants found !! Meal seeding skipped.');
            return;
        }

        $ingredientsResponse = Http::get('https://www.themealdb.com/api/json/v1/1/list.php?i=list');
        $apiIngredients = $ingredientsResponse->json('meals');

        if (!$apiIngredients) {
            $this->command->warn('No ingredients fetched from API.');
            return;
        }

        $insertedIngredients = collect($apiIngredients)->map(function ($ing) {
            return Ingredient::create([
                'name' => strtolower($ing['strIngredient']),
            ]);
        });

        $ingredients = $insertedIngredients->keyBy(fn($i) => strtolower($i->name));

        $mealsResponse = Http::get('https://www.themealdb.com/api/json/v1/1/search.php?f=c');
        $apiMeals = $mealsResponse->json('meals');

        if (!$apiMeals) {
            $this->command->warn('No meals fetched from API.');
            return;
        }

        foreach ($restaurants as $restaurant) {
            collect($apiMeals)->take(10)->each(function ($apiMeal) use ($restaurant, &$ingredients) {

                $category = Category::inRandomOrder()->first();

                $meal = new Meal();
                $meal->name = $apiMeal['strMeal'];
                $meal->description = $apiMeal['strInstructions'] ?? 'No description';
                $meal->rate = rand(1, 5);
                $meal->delivery_time = rand(15, 60);
                $meal->is_available = rand(0, 1);
                $meal->restaurant_id = $restaurant->id;
                $meal->category_id = $category->id;
                $meal->cheif_id = Cheif::inRandomOrder()->first()->id;
                $meal->save();

                $meal->image()->create([
                    'url' => $apiMeal['strMealThumb'] ?? 'https://cdn-icons-png.freepik.com/256/8449/8449978.png',
                ]);

                $ingredientNames = collect(range(1, 20))
                    ->map(fn($i) => strtolower($apiMeal["strIngredient{$i}"] ?? null))
                    ->filter(fn($name) => !empty($name) && $ingredients->has($name));

                $ingredientIds = $ingredientNames->map(fn($name) => $ingredients[$name]->id)->toArray();
                $meal->ingredients()->attach($ingredientIds);

                MealVariant::factory()->count(3)->create([
                    'meal_id' => $meal->id,
                ]);
            });
        }

        $this->command->info('Ingredients, categories, and real meals seeded successfully!');
    }
}
