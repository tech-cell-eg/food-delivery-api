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
        return response()->json([
          'status' => false,
          'message' => 'Email already exists',
        ], 409);
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
      return response()->json([
        'status' => true,
        'message' => 'User registered successfully',
        'data' => $user
      ], 201);
    } catch (QueryException $e) {
      DB::rollBack();
      return response()->json([
        'status' => false,
        'message' => 'Database error during registration, please try again later',
        'error' => $e->getMessage()
      ], 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'status' => false,
        'message' => 'User registration failed, please try again',
        'error' => $e->getMessage()
      ], 500);
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

      return response()->json([
        'status' => true,
        'message' => 'Logged out successfully'
      ]);
    } catch (JWTException $e) {
      return response()->json([
        'status' => false,
        'message' => 'Token missing or invalid'
      ], 401);
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'message' => 'Something went wrong during logout',
        'error' => $e->getMessage()
      ], 500);
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

      return response()->json([
        'status' => true,
        'message' => 'Token refreshed successfully',
        'token' => $newToken,
        'token_type' => 'bearer',
      ]);
    } catch (JWTException $e) {
      return response()->json([
        'status' => false,
        'message' => 'Token missing or invalid'
      ], 401);
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'message' => 'Something went wrong during token refresh',
        'error' => $e->getMessage()
      ], 500);
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
      return response()->json([
        'error' => 'Invalid OTP',
        'status' => false,
        'message' => 'Invalid OTP, please check your email and try again'
      ], 400);
    }

    if (now()->gt($otpData->expires_at)) {
      return response()->json([
        '
      error' => 'OTP expired',
        'status' => false,
        'message' => 'OTP expired, please request a new one'
      ], 400);
    }
    DB::beginTransaction();
    try {
      $user = User::where('email', $request->email)->first();
      if (!$user) {
        return response()->json([
          'error' => 'User not found',
          'status' => false,
          'message' => 'User not found, please register first'
        ], 404);
      }

      if ($user->is_verified) {
        return response()->json([
          'error' => 'User already verified',
          'status' => false,
          'message' => 'User already verified'
        ], 400);
      }
    } catch (QueryException $e) {
      DB::rollBack();
      return response()->json([
        'error' => 'Database error',
        'status' => false,
        'message' => 'Database error during OTP verification, please try again later'
      ], 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'error' => 'Unexpected error',
        'status' => false,
        'message' => 'An unexpected error occurred during OTP verification, please try again'
      ], 500);
    }

    // Update user verification status
    $user->is_verified = true;
    $user->email_verified_at = now();
    $user->save();
    DB::commit();

    $otpData->delete();

    return response()->json(['message' => 'Email verified. Account activated!', 'status' => true], 200);
  }
  public function resendOtp(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json([
        'error' => 'User not found',
        'status' => false,
        'message' => 'User not found, please register first'
      ], 404);
    }

    if ($user->is_verified) {
      return response()->json([
        'error' => 'User already verified',
        'status' => false,
        'message' => 'User already verified'
      ], 400);
    }

    $otp = rand(100000, 999999);
    $expiresAt = now()->addMinutes(5);

    Otp::updateOrCreate(
      ['email' => $user->email],
      ['otp' => $otp, 'expires_at' => $expiresAt]
    );

    Mail::to($user->email)->send(new OtpMail($otp));

    return response()->json(['message' => 'OTP resent successfully', 'status' => true], 200);
  }
}
