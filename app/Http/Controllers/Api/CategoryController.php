<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

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
}
