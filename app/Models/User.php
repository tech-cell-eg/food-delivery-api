<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'provider',
    'provider_id',
    'provider_token',
    'otp',
    'otp_expires_at'
  ];

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }
  public function getJWTCustomClaims()
  {
    return [];
  }
  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function hasValidOtp($otp)
  {
    return Hash::check($otp, $this->otp) && $this->otp_expires_at && $this->otp_expires_at->isFuture();
  }

  public function clearOtp()
  {
    $this->update([
      'otp' => null,
      'otp_expires_at' => null,
    ]);
  }
  public function addresses()
  {
    return $this->hasMany(Address::class);
  }
  public function orders()
  {
    return $this->hasMany(Order::class);
  }
  public function image()
  {
    return $this->morphOne(Image::class, 'imageable');
  }
  public function cheif()
  {
    return $this->hasOne(Cheif::class);
  } 
}
