<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;



class Category extends Model
{

    protected $fillable = ['name'];

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
        return $this->hasMany(Meal::class);
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
