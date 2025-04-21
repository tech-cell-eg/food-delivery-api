<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantShowResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'delivery_fee' => $this->delivery_fee,
            'opening_hours' => [
                'open_at' => $this->open_at,
                'close_at' => $this->close_at,
                'is_open' => $this->isOpenNow()
            ],
            'delivery_time' => $this->delivery_time,
            'rating' => $this->rate,

            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'image_url' => $category->image_url
                    ];
                });
            }),

            'meals' => $this->whenLoaded('meals', function () {
                return $this->meals->map(function ($meal) {
                    return [
                        'id' => $meal->id,
                        'name' => $meal->name,
                        'description' => $meal->description,
                        'price' => $meal->price,
                        'image_url' => $meal->image_url
                    ];
                });
            }),
        ];
    }
}
