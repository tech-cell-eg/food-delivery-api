<?php

namespace App\Http\Controllers;

use App\RepositoryInterface\UserInterface;
use App\Responses\responseApi;
use App\Http\Requests\{LoginRequest, RegisterChefRequest, UpdateProfileRequest, RegisterRequest};
use Illuminate\Support\Facades\{DB, Auth, Mail};
use Illuminate\Database\QueryException;
use App\Http\Resources\UserResource;
use App\Models\{User, Otp};
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Mail\OtpMail;
use Exception;

class AuthController extends Controller
{
  use ResponseApi;

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
      $expiresAt = now()->addMinutes(5)->timezone('Africa/Cairo')->format('Y-m-d H:i:s');

      Otp::updateOrCreate(
        ['email' => $user->email],
        ['otp' => $otp, 'expires_at' => $expiresAt]
      );
      Mail::to($user->email)->send(new OtpMail($otp));

      DB::commit();
      return $this->responseSuccess('User registered successfully, please verify your email', [
        'otp' => $otp,
        'expires_at' => $expiresAt
      ], 201);
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseSuccess('Database error during registration', $e->getMessage());
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseSuccess('An unexpected error occurred', $e->getMessage());
    }
  }

  public function login(LoginRequest $request)
  {
    $credentials = $request->validated();

    if (! Auth::attempt($credentials)) {
      return $this->responseError('Invalid credentials', 401);
    }

    DB::beginTransaction();
    try {
      $user = Auth::user();

      if (! $user->is_verified) {
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5)->timezone('Africa/Cairo')->format('Y-m-d H:i:s');

        Otp::updateOrCreate(
          ['email' => $user->email],
          ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::to($user->email)->send(new OtpMail($otp));

        return $this->responseSuccess('User not verified', [
          'otp' => $otp,
          'expires_at' => $expiresAt,
        ], 200);
      }

      $token = JWTAuth::fromUser($user);

      DB::commit();

      $user = new UserResource($user);
      $user['token'] = $token;

      return $this->responseSuccess(
        'User logged in successfully',
        $user
      );
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseSuccess('Database error during login', $e->getMessage());
    } catch (JWTException $e) {
      DB::rollBack();
      return $this->responseSuccess('Could not create token', $e->getMessage());
    } catch (Exception $e) {
      DB::rollBack();
      return $this->responseSuccess('An unexpected error occurred', $e->getMessage());
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
      return $this->responseSuccess('Token missing or invalid', $e->getMessage(), 401);
    } catch (\Exception $e) {
      return $this->responseSuccess('Something went wrong during logout', $e->getMessage());
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
      return $this->responseSuccess('Token missing or invalid', $e->getMessage(), 401);
    } catch (\Exception $e) {
      return $this->responseSuccess('Something went wrong during token refresh', $e->getMessage());
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

      if (! $user) {
        return $this->responseError('User not found', 404);
      }

      if ($user->is_verified) {
        return $this->responseError('User already verified', 400);
      }
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseSuccess('Database error during OTP verification', $e->getMessage());
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseSuccess('An unexpected error occurred', $e->getMessage());
    }

    // Update user verification status
    $user->is_verified = true;
    $user->email_verified_at = now();
    $user->save();
    $token = JWTAuth::fromUser($user);

    $user = new UserResource($user);
    $user['token'] = $token;
    DB::commit();

    $otpData->delete();

    return $this->responseSuccess(
      'OTP verified successfully',
      $user
    );
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
    $expiresAt = now()->addMinutes(5)->timezone('Africa/Cairo')->format('Y-m-d H:i:s');

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
    $user = Auth::user()?->withRelationshipAutoloading();

    if (! $user) {
      return $this->responseError('User not found', 404);
    }

    return $this->responseSuccess('User retrieved successfully', new UserResource($user));
  }

  // This part for chef
  public function registerChef(RegisterChefRequest $request)
  {
    try {
      DB::beginTransaction();

      $data = $request->validated();

      if ($this->user->checkEmailExists($data['email'])) {
        return $this->responseError('Email already exists', 409);
      }

      $user = $this->user->registerChef($data);
      $otp = rand(100000, 999999);
      $expiresAt = now()->addMinutes(5)->timezone('Africa/Cairo')->format('Y-m-d H:i:s');

      Otp::updateOrCreate(
        ['email' => $user->email],
        ['otp' => $otp, 'expires_at' => $expiresAt]
      );

      Mail::to($user->email)->send(new OtpMail($otp));

      DB::commit();

      return $this->responseSuccess('User registered successfully, please verify your email', [
        'otp' => $otp,
        'expires_at' => $expiresAt
      ], 201);
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseSuccess('Database error during registration', $e->getMessage());
    } catch (Exception $e) {
      DB::rollBack();
      return $this->responseSuccess('An unexpected error occurred',  $e->getMessage());
    }
  }

  public function profile(UpdateProfileRequest $request)
  {
    $data = $request->validated();
    /** @var Request|UpdateProfileRequest $request */
    $image = $request->file('image');

    $user = $this->user->updateUserProfile(Auth::user(), $data, $image);

    return $this->responseSuccess(
      'User profile updated successfully',
      new UserResource($user)
    );
  }
}
