<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'img'           => $this->image_url,
            'desc'          => $this->description,
            'rate'          => (float) $this->rate,
            'deliveryFees'  => (float) $this->delivery_fee,
            'deliveryTime'  => (int) $this->delivery_time,
            'categories'    => $this->categories->pluck('name'),
            'is_open'       => $this->isOpenNow(),
            'meals'         => MealResource::collection($this->meals),
        ];
    }
}
