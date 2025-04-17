<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'phone',
        'email',
        'delivery_fee',
        'open_at',
        'close_at',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image->url) : null;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

}
