<?php

namespace App\Http\Controllers\Api;

use App\Models\Restaurant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantIndexResource;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantShowResource;

class RestaurantController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $restaurants = Restaurant::with(['categories', 'image'])->paginate(5);

        if ($restaurants->isEmpty()) {
            return $this->errorResponse('no data found');
        }
        return $this->successResponse([
            'restaurants' => RestaurantIndexResource::collection( $restaurants ),
            'meta'             => [
                'total'        => $restaurants->total(),
                'per_page'     => $restaurants->perPage(),
                'current_page' => $restaurants->currentPage(),
                'last_page'    => $restaurants->lastPage(),
                'from'         => $restaurants->firstItem(),
                'to'           => $restaurants->lastItem(),
                'links'        => [
                    'first' => $restaurants->url(1),
                    'last'  => $restaurants->url($restaurants->lastPage()),
                    'prev'  => $restaurants->previousPageUrl(),
                    'next'  => $restaurants->nextPageUrl(),
                ],
            ]
        ]);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with(['reviews', 'categories', 'meals.variants'])
            ->find($id);
        if (!$restaurant) {
            return $this->errorResponse('Restaurant not found or has no available meals', 404);
        }

        return $this->successResponse(new RestaurantShowResource($restaurant), 'Restaurant retrieved successfully');
    }
}
