<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\CheckToken;



Route::group(['prefix' => 'v1'], function () {
  // Categories Routes
  Route::get('/categories', [CategoryController::class, 'index'])->middleware('jwt.auth');
});
Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('jwt:auth');
Route::prefix('auth/')->controller(AuthController::class)->group(function () {
  Route::post('register', 'register');
  Route::post('login', 'login');
  Route::post('logout', 'logout')->middleware('jwt.auth');
  Route::post('refreshtoken', 'refreshToken')->middleware('jwt.auth');
});
