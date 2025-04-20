<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;


class Otp extends Model
{
  //
  use HasFactory;
  protected $fillable = [
    'email',
    'otp',
    'expires_at',
  ];
  public function isExpired()
  {
    return Carbon::now()->gt($this->expires_at);
  }
}
