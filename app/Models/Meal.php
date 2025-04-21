<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Meal extends Model

{
    /** @use HasFactory<\Database\Factories\MealFactory> */
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'rate',
        'delivery_time',
        'is_available',
        'category_id',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function variants()
    {
        return $this->hasMany(MealVariant::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image->url) : null;
    }
    public function cheif()
    {
        return $this->belongsTo(Cheif::class, 'cheif_id');
    }
    public function orderMeals()
{
    return $this->hasMany(OrderMeal::class);
}

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'meal_ingredient');
    }
}
