<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;


class Category extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'slug'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class);
    }
    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'category_meal');
    }

    public function getAverageRatingAttribute()
    {
        return $this->restaurants()
            ->withAvg('reviews', 'rating')
            ->get()
            ->avg('reviews_avg_rating') ?? 0;
    }

    public function getRestaurantsCountAttribute()
    {
        return $this->restaurants()->count();
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image->url) : null;
    }
}
