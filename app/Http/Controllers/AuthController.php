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
      
      if($this->user->checkEmailExists($data['email'])) {
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
    try {
      DB::beginTransaction();
      $data = $request->validated();
      if ($this->user->login($data)) {
        $user= Auth::user();
        DB::commit();
        return $this->responseSuccess('User logged in successfully', [
          'token' =>$user-> createToken('api_token')->plainTextToken,
          'user' => $user
        ], 201);
      } else {
        DB::rollBack();
        return $this->responseError('Invalid credentials', 401);
      }
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseError('Database error during login, please try again later', 401);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseError('User login failed, please try again' . $e->getMessage(), 401);
    }
  }
  public function logout()
  {
    try {
      DB::beginTransaction();
      $this->user->logout();
      DB::commit();
      return $this->responseSuccess('User logged out successfully');
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->responseError('Database error during logout, please try again later', 401);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->responseError('User logout failed, please try again' . $e->getMessage(), 401);
    }
  }
}
