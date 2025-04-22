<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMeal extends Model
{
    /** @use HasFactory<\Database\Factories\OrderMealFactory> */
    use HasFactory;
    protected $table = 'order_meal';

    protected $fillable = [
        'order_id',
        'meal_id',
        'meal_variant_id',
        'quantity',
        'price',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
