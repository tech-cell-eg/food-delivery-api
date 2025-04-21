<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Middleware\CheckToken;
use App\Http\Controllers\CheifController;

Route::group(['prefix' => 'v1' ,'middleware' => 'jwt.auth'], function () {
        // Categories Routes
        Route::get('/categories', [CategoryController::class,'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        // Restaurant
        Route::get('/restaurants', [RestaurantController::class,'index']);
        Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);

        Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);

        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{address}', [AddressController::class, 'show'])->name('addresses.show');

        Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
        Route::get('/meals/{id}', [MealController::class, 'show'])->name('meals.show');
        
});
Route::get('/cheifstatistics/{id}', [cheifController::class, 'statistics']);

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('jwt:auth');

Route::prefix('auth/')->controller(AuthController::class)->group(function () {
  Route::post('register', 'register');
  Route::post('verify-otp', 'verifyOtp');
  Route::post('resend-otp', 'resendOtp');
  Route::post('login', 'login');
  Route::post('logout', 'logout')->middleware('jwt.auth');
  Route::post('refreshtoken', 'refreshToken')->middleware('jwt.auth');

});
