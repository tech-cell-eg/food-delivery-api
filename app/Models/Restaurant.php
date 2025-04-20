<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Model
{

    protected $fillable = [
        'name',
        'description',
        'delivery_fee',
        'delivery_time',
        'open_at',
        'close_at',
        'average_delivery_time'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image->url) : null;
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
