<?php

namespace App\Http\Controllers\Api;

use App\Models\Restaurant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;

class RestaurantController extends Controller
{
    use ApiResponse;

    public function show($id)
    {
        $restaurant = Restaurant::with(['reviews.user', 'categories', 'image'])->find($id);

        if (!$restaurant) {
            return $this->errorResponse('No restaurant found', 404);
        }

        return $this->successResponse(new RestaurantResource($restaurant), 'Restaurant retrieved successfully');
    }
}
