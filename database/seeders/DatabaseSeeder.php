<?php

namespace Database\Seeders;


use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MealVariant;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      User::factory(10)->create();
      $this->call([
        RestaurantSeeder::class,
      ]);

      $this->call([

          
          CheifSeeder::class,
            RatingSeeder::class,
          ]);
                    
        $this->call([
            CategorySeeder::class,
            RestaurantSeeder::class,
            IngredientSeeder::class,
            MealSeeder::class
        ]);
        Meal::factory(10)->create();
      MealVariant::factory(10)->create();
        OrderMeal::factory(10)->create();
      
        Order::factory(10)->create();

    }
}
