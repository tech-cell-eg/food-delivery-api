<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

Route::group(['prefix' => 'v1'], function () {
    // Categories Routes
    Route::get('/categories', [CategoryController::class, 'index']);
});
