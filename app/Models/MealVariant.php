<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealVariant extends Model
{
  use HasFactory;
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
