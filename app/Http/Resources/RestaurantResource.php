<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'delivery_fee' => $this->delivery_fee,
            'open_at' => $this->open_at?->format('H:i'),
            'close_at' => $this->close_at?->format('H:i'),
            'image_url' => $this->image_url,
            'average_rating' => round($this->average_rating, 1),
            'reviews' => ReviewResource::collection($this->reviews),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
