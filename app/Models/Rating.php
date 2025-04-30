<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;
    public function cheif()
{
    return $this->belongsTo(Cheif::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

}
