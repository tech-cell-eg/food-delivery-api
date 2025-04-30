<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'meal_id', 'meal_variant_id', 'quantity'];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MealVariant::class, 'meal_variant_id');
    }
}
