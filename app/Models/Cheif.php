<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Cheif  extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\CheifFactory> */
    use HasFactory;
    protected $fillable = [
        'email',
        'password',
        'experience',
        'address',
    
        'name',
        'user_id',
        'specialty',
        'phone',
        'fcm_token',
    ];
    public function getJWTIdentifier()
    {
      return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
      return [];
    }
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
    public function ratings()
{
    return $this->hasMany(Rating::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}

}
