<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MealRequest;
use App\Models\Meal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Cheif;

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
    public function store(MealRequest $request)
    {
      $user = Auth::user();
      
        // Find the chief ID based on the authenticated user
        // Validate and create a new meal
        $meal = Meal::create([
            'name' => $request->name,
            'description' => $request->description,
            'rate' => $request->rate,
            'restaurant_id' => $request->restaurant_id,
            'category_id' => $request->category_id,
            'is_available' => $request->is_available,
            'cheif_id' => $user->id,
            'delivery_time' => $request->delivery_time,
        ]);

        return $this->successResponse(
            new MealResource($meal),
            'Meal created successfully',
            201
        );
    }
    public function update(MealRequest $request, $id)
    {
        // Find the meal by ID
        $meal = Meal::find($id);

        if (!$meal) {
            return $this->errorResponse('Meal not found', 404);
        }

        // Update the meal with the validated data
        $meal->update($request->all());

        return $this->successResponse(
            new MealResource($meal),
            'Meal updated successfully'
        );
    }
    public function destroy($id)
    {
        // Find the meal by ID
        $meal = Meal::find($id);

        if (!$meal) {
            return $this->errorResponse('Meal not found', 404);
        }
        

        // Delete the meal
        $meal->delete();

        return $this->successResponse(
            null,
            'Meal deleted successfully'
        );
    }
}

