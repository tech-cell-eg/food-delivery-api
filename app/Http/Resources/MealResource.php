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
            'time' => $this->delivery_time,
            'rate' => (float) $this->rate,
            'name' => $this->name,
            'category' => $this->category->name,
            'ingredients' => $this->ingredients->pluck('name'),
            'img' => $this->image_url,
            'desc' => $this->description,
            'sizes' => MealVariantResource::collection($this->variants()->get()),
        ];
    }
}
