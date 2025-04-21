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
        $query = Meal::where('is_available', true);

        if ($request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $meals = $query->with(['restaurant', 'categories', 'variants', 'image', 'ingredients'])->get();

        return $this->successResponse(MealResource::collection($meals), 'Meals retrieved successfully');
    }

    public function show($id)
    {
        $meal = Meal::where('is_available', true)
            ->whereHas('variants', function ($q) {
                $q->where('is_available', true);
            })
            ->with(['restaurant', 'categories', 'variants'])
            ->find($id);

        if (!$meal) {
            return $this->errorResponse('Meal not found or unavailable', 404);
        }

        return $this->successResponse(new MealResource($meal), 'Meal retrieved successfully');
    }
}
