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
        $categories = Category::with('image')->paginate(5);
        if ($categories->isEmpty()) {
            return $this->errorResponse('No categories found');
        }
        return $this->successResponse([
            'categories' => CategoryResource::collection($categories),
        'meta'             => [
            'total'        => $categories->total(),
            'per_page'     => $categories->perPage(),
            'current_page' => $categories->currentPage(),
            'last_page'    => $categories->lastPage(),
            'from'         => $categories->firstItem(),
            'to'           => $categories->lastItem(),
            'links'        => [
                'first' => $categories->url(1),
                'last'  => $categories->url($categories->lastPage()),
                'prev'  => $categories->previousPageUrl(),
                'next'  => $categories->nextPageUrl(),
            ],
        ],], 'Categories fetched successfully');
    }
    public function show($id)
    {
        $category = Category::with(['restaurants', 'image'])->find($id);

        if (!$category) {
            return $this->errorResponse('No data found', 404);
        }

        return $this->successResponse(new CategoryResource($category), 'Retrieved data successfully');
    }
}
