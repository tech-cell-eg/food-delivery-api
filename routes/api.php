<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\CheckToken;



Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:api');
Route::prefix('auth/')->controller(AuthController::class)->group(function () {
  Route::post('register', 'register');
  Route::post('login', 'login');
  Route::post('logout', 'logout')->middleware('jwt.auth');
  Route::post('refreshtoken', 'refreshToken')->middleware('jwt.auth');
});
