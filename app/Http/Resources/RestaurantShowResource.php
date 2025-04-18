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
            'slug' => $this->slug,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'image_url' => $this->image_url,
            'delivery_fee' => $this->delivery_fee,
            'opening_hours' => [
                'open_at' => $this->open_at,
                'close_at' => $this->close_at,
                'is_open' => $this->isOpenNow()
            ],
            'average_delivery_time' => $this->average_delivery_time,
            'average_rating' => round($this->average_rating, 1),
            'reviews_count' => $this->whenCounted('reviews'),

            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
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

            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user_name' => $review->user->name,
                        'created_at' => $review->created_at->diffForHumans()
                    ];
                });
            })
        ];
    }
}
