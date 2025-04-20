<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MealVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant' => [
                'id' => $this->restaurant->id,
                'name' => $this->restaurant->name,
            ],
            'name' => $this->name,
            'description' => $this->description,
            'is_available' => $this->is_available,
            'image_url' => $this->image_url,
            'categories' => CategoryResource::collection($this->categories),
            'variants' => MealVariantResource::collection($this->variants()->where('is_available', true)->get()),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];

    }
}
