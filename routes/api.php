<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RestaurantController;

Route::group(['prefix' => 'v1'], function () {
        // Categories Routes
        Route::get('/categories', [CategoryController::class,'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        // Restaurant
        Route::get('/restaurants', [RestaurantController::class,'index']);
        Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);

        Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);

        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{id}', [AddressController::class, 'show'])->name('addresses.show');

        Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
        Route::get('/meals/{id}', [MealController::class, 'show'])->name('meals.show');
});
