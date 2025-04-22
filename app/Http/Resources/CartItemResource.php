<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mealPrice = $this->variant->price;
        $itemTotal = $mealPrice * $this->quantity;

        return [
            'cart_item_id' => $this->id,
            'name' => $this->meal->name,
            'image' => $this->meal->image->url ?? null,
            'size' => $this->variant->size,
            'quantity' => (int) $this->quantity,
            'price' => (float) $mealPrice,
            'item_total' => $itemTotal,
        ];
    }
}
