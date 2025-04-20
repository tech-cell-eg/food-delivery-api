<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealVariant extends Model
{
    protected $fillable = [
        'meal_id',
        'size',
        'price'
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
