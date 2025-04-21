<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;

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
        'average_delivery_time'
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
    public function isOpenNow()
    {
        $now = now()->format('H:i');
        return $now >= $this->open_at && $now <= $this->close_at;
    }

}
