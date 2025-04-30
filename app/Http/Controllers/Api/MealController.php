<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;

class MealController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $meals = Meal::query()
            ->where('is_available', true)
            ->when($request->restaurant_id, fn ($q) =>
                $q->where('restaurant_id', $request->restaurant_id)
            )
            ->with([
                'restaurant',
                'category',
                'variants',
                'image',
                'ingredients'
            ])
            ->get();

        return $this->successResponse(
            MealResource::collection($meals),
            'Meals retrieved successfully'
        );
    }

    public function show($id)
    {
        $meal = Meal::where('is_available', true)
            ->with([
                'restaurant',
                'category',
                'variants',
                'image',
                'ingredients'
            ])
            ->find($id);

        if (!$meal) {
            return $this->errorResponse('Meal not found or unavailable', 404);
        }

        return $this->successResponse(
            new MealResource($meal),
            'Meal retrieved successfully'
        );
    }
    public function store(Request $request)
    {
        // Validate and create a new meal
        $meal = Meal::create($request->all());

        return $this->successResponse(
            new MealResource($meal),
            'Meal created successfully',
            201
        );
    }
}

