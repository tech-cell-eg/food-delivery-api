<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->image_url,
            "restaurants" => $this->restaurants->map(function ($restaurant) {
                return [
                    "id" => $restaurant->id,
                    "name" => $restaurant->name,
                    "image" => $restaurant->image_url,
                    "rating" => $restaurant->rating,
                ];
            }),
        ];
    }
}
