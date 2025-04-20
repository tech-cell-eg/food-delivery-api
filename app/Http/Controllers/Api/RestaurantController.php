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

    public function index(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'nullable|numeric|min:0|max:5',
            'delivery_time' => 'nullable|integer|min:1',
        ]);

        $restaurants = Restaurant::with(['categories', 'image']);

        if ($request->filled('rating')) {
            $restaurants->where('rating', '>=', $request->rating);
        }

        if ($request->filled('delivery_time')) {
            $restaurants->where('delivery_time', '<=', $request->delivery_time);
        }

        $restaurants = $restaurants->paginate(5);

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
