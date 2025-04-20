<?php

namespace App\Repository;


use App\Models\User;

use App\RepositoryInterface\UserInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class UserRepository implements UserInterface
{

  public function register(array $data)
  {
    $data['password'] = Hash::make($data['password']);
    $data['email_verified_at'] = now();

    $image_path = null;
    if (isset($data['image'])) {
      $image_path = $data['image']->store('images', 'public');
    }

    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => $data['password'],
      'phone' => $data['phone'],
      'is_verified' => false,

    ]);

    if ($image_path) {
      $user->image()->create([
        'url' => $image_path,
      ]);
    }

    return $user->load('image');
  }

  public function login(array $data)
  {


    return Auth::attempt($data);
  }

  public function logout()
  {
    return Auth::user()->tokens()->delete();
  }
  public function getUserById($id)
  {
    return User::find($id);
  }
  public function updateUser($id, array $data)
  {
    $user = User::find($id);
    if ($user) {
      $user->update($data);
      return $user;
    }
    return null;
  }
  public function deleteUser($id)
  {
    $user = User::find($id);
    if ($user) {
      $user->delete();
      return true;
    }
    return false;
  }
  public function getAllUsers()
  {
    return User::all();
  }
  public function getUserByEmail($email)
  {
    return User::where('email', $email)->first();
  }
  public function getUserByPhone($phone)
  {
    return User::where('phone', $phone)->first();
  }
  public function updateUserPassword($id, $password)
  {
    $user = User::find($id);
    if ($user) {
      $user->password = Hash::make($password);
      $user->save();
      return $user;
    }
    return null;
  }
  public function updateUserEmail($id, $email)
  {
    $user = User::find($id);
    if ($user) {
      $user->email = $email;
      $user->save();
      return $user;
    }
    return null;
  }
  public function checkEmailExists($email)
  {
    return User::where('email', $email)->exists();
  }
}
