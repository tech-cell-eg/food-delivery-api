<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "name"          => $this->name,
            "email"         => $this->email,
            "phone"         => $this->phone,
            "role"          => $this->role,
            "description"   => $this->cheif?->description,
            "fcm_token"     => $this->cheif?->fcm_token,
            "delivery_fee"  => $this->cheif?->delivery_fee,
            "delivery_time" => $this->cheif?->delivery_time,
            "rate"          => $this->cheif?->rate,
            "images"        => $this->cheif?->images ?
                $this->cheif->images->pluck('url')->map(fn($url) => asset('storage/' . $url)) :
                [],
        ];
    }
}
