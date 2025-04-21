<?php

namespace App\Http\Controllers\Api;

use App\Models\Restaurant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\RestaurantIndexResource;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantShowResource;

class RestaurantController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $restaurants = Restaurant::with([
            'categories',
            'image',
            'meals',
            'meals.variants',
            'meals.ingredients',
            'meals.image',
            'meals.category'
        ])->paginate(5);

        if ($restaurants->isEmpty()) {
            return $this->errorResponse('no data found');
        }

        return $this->successResponse([
            'restaurants' => RestaurantIndexResource::collection($restaurants),
            'meta' => new PaginationResource($restaurants),
        ]);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with(['categories', 'meals.variants'])
            ->find($id);

        if (!$restaurant) {
            return $this->errorResponse('Restaurant not found or has no available meals', 404);
        }

        return $this->successResponse(new RestaurantShowResource($restaurant), 'Restaurant retrieved successfully');
    }
}
