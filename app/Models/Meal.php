<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Meal extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'slug',
        'description',
        'is_available',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_meal');
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
}
