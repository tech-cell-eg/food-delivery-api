<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\RepositoryInterface\UserInterface;
use App\Models\Cheif;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Support\Facades\Validator;
class CheifAuthController extends Controller
{
    //
  
use ApiResponse;

protected $user;
public function __construct(UserInterface $user)
{
  $this->user = $user;
}
    public function register(Request $request){
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'specialty' => 'required|string',
        'experience' => 'nullable|string', 
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'fcm_token' => 'required|string',
    ]);
    if ($validator->fails()) {
      return $this->errorResponse($validator->errors(), 422);
    }
    $user = $this->user->register($request->all());
    if (!$user) {
      return $this->errorResponse('User registration failed', 500);
    }
    $cheif = Cheif::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
    
      'user_id' => $user->id,
      'specialty' => $request->specialty,
      'experience' => $request->experience,
      'phone' => $request->phone,
      'address' => $request->address,
      'fcm_token' => $request->fcm_token,
  ]);
    if (!$cheif) {
      return $this->errorResponse('Cheif registration failed', 500);
    }
    return $this->successResponse('Cheif registered successfully', [
    
      'cheif' => $cheif
  ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
    
        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Unauthorized', 401);
        }
    
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);
    
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
    
        $cheif = $user->cheif;
        if (!$cheif) {
            return $this->errorResponse('Cheif not found', 404);
        }
    
        return $this->successResponse('User logged in successfully', [
            'user' => $cheif,
            'token' => $token,
        ], 200);
    }
    
    public function logout()
    {
        Auth::logout();

        return $this->successResponse('User logged out successfully', [], 200);
    }

}

