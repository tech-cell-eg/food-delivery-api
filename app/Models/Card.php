<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'payment_method_id',
        'brand',
        'last4',
        'exp_month',
        'exp_year'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
