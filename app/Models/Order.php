<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'address_id',
        'offer_id',
        'subtotal',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'order_meal')
                    ->withPivot('meal_variant_id', 'quantity', 'price')
                    ->withTimestamps();
    }
    public function cheif()
    {
        return $this->belongsTo(Cheif::class);
    }
    public function orderMeals()
{
    return $this->hasMany(OrderMeal::class);
}

}
