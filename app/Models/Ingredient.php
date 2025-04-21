<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $table = 'ingredients';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function meals()
    {
        return $this->belongsToMany(Meal::class);
    }
}
