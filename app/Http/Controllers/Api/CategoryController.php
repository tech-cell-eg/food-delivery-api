<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryShowResource;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::with('image')->get();
        if ($categories->isEmpty()) {
            return $this->errorResponse('No categories found');
        }
        return $this->successResponse(CategoryResource::collection($categories), 'Categories fetched successfully');
    }
    public function show($id)
    {
        $category = Category::with(['restaurants', 'image'])->find($id);

        if (!$category) {
            return $this->errorResponse('No data found', 404);
        }

        return $this->successResponse(new CategoryShowResource($category), 'Retrieved data successfully');
    }
}
