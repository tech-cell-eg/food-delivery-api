<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheif extends Model
{
    /** @use HasFactory<\Database\Factories\CheifFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
    public function ratings()
{
    return $this->hasMany(Rating::class);
}

}
