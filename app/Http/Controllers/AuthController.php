<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\RepositoryInterface\UserInterface;
use App\Responses\responseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

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

      if (!$token = Auth::attempt($credentials)) {
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

  public function refreshToken(){
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

}
