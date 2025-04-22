<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\RepositoryInterface\UserInterface;
use App\Responses\responseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
  use ResponseApi;
  //
  protected $user;
  public function __construct(UserInterface $user)
  {
    $this->user = $user;
  }
  public function register(RegisterRequest $request)
  {

    try {

      
      DB::beginTransaction();

      $data = $request->validated();

      if ($this->user->checkEmailExists($data['email'])) {
        return $this->responseError('Email already exists', 409);
      }


      $user = $this->user->register($data);
      $otp = rand(100000, 999999);
      $expiresAt = now()->addMinutes(5);

      Otp::updateOrCreate(
        ['email' => $user->email],
        ['otp' => $otp, 'expires_at' => $expiresAt]
      );
      Mail::to($user->email)->send(new OtpMail($otp));

      DB::commit();
      return $this->responseSuccess('User registered successfully, please verify your email', [
        'user' => $user,
        'otp' => $otp,
        'expires_at' => $expiresAt
      ], 201);
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseError('Database error during registration', 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseError('An unexpected error occurred', 500);
    }
  }
  public function login(LoginRequest $request)
  {



    DB::beginTransaction();

    try {
      $credentials = $request->validated();

      if (! Auth::attempt($credentials)) {
        DB::rollBack();
        return $this->responseError('Invalid credentials', 401);
      }

      $user = Auth::user();

      $token = JWTAuth::fromUser($user);

      DB::commit();

      return $this->responseSuccess('User logged in successfully', [
        'token' => $token,
        'token_type' => 'bearer',
        'user' => $user,
      ], 200);
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseError('Database error during login', 500);
    } catch (JWTException $e) {
      DB::rollBack();
      return $this->responseError('Could not create token', 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseError('An unexpected error occurred', 500);
    }
  }
  public function logout()
  {
    try {
      // Check if token exists
      if (!JWTAuth::getToken()) {
        throw new JWTException('Token not provided');
      }

      // Invalidate the token
      JWTAuth::invalidate(JWTAuth::getToken());

      return $this->responseSuccess('User logged out successfully', [], 200);
    } catch (JWTException $e) {
      return $this->responseError('Token missing or invalid', 401);
    } catch (\Exception $e) {
      return $this->responseError('Something went wrong during logout', 500);
    }
  }

  public function refreshToken()
  {
    try {
      // Check if token exists
      if (!JWTAuth::getToken()) {
        throw new JWTException('Token not provided');
      }

      // Refresh the token
      $newToken = JWTAuth::refresh(JWTAuth::getToken());

      return $this->responseSuccess('Token refreshed successfully', [
        'token' => $newToken,
        'token_type' => 'bearer',
      ], 200);
    } catch (JWTException $e) {
      return $this->responseError('Token missing or invalid', 401);
    } catch (\Exception $e) {
      return $this->responseError('Something went wrong during token refresh', 500);
    }
  }
  public function verifyOtp(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'otp' => 'required|string'
    ]);

    $otpData = Otp::where('email', $request->email)
      ->where('otp', $request->otp)
      ->first();

    if (!$otpData) {
      return $this->responseError('Invalid OTP', 400);
    }

    if (now()->gt($otpData->expires_at)) {
      return $this->responseError('OTP expired', 400);
    }
    DB::beginTransaction();
    try {
      $user = User::where('email', $request->email)->first();
      if (!$user) {
        return $this->responseError('User not found', 404);
      }

      if ($user->is_verified) {
        return $this->responseError('User already verified', 400);
      }
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseError('Database error during OTP verification', 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseError('An unexpected error occurred', 500);
    }

    // Update user verification status
    $user->is_verified = true;
    $user->email_verified_at = now();
    $user->save();
    DB::commit();

    $otpData->delete();

    return $this->responseSuccess('OTP verified successfully', [
      'user' => $user,
    ], 200);
  }
  public function resendOtp(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return $this->responseError('User not found', 404);
    }

    if ($user->is_verified) {
      return $this->responseError('User already verified', 400);
    }

    $otp = rand(100000, 999999);
    $expiresAt = now()->addMinutes(5);

    Otp::updateOrCreate(
      ['email' => $user->email],
      ['otp' => $otp, 'expires_at' => $expiresAt]
    );

    Mail::to($user->email)->send(new OtpMail($otp));

    return $this->responseSuccess('OTP resent successfully', [
      'otp' => $otp,
      'expires_at' => $expiresAt
    ], 200);
  }
  public function me()
  {
      return response()->json(auth()->user());
  }
  
  
  
}
