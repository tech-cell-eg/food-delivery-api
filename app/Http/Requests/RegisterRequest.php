<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class RegisterRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }
  public function wantsJson()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name'      => 'required|string|max:255',
      'email'     => 'required|string|email|max:255|unique:users',
      'bio'       => 'string',
      'password'  => 'required|string|min:8',
      'phone'     => 'string|max:11',
      'image'     => 'image|mimes:jpeg,png,jpg,gif|max:2048',
      'fcm_token' => 'required|string',
    ];
  }

  public function messages()
  {
    return [
      'name.required'     => 'The name field is required.',
      'name.string'       => 'The name must be a string.',
      'name.max'          => 'The name must not be greater than 255 characters.',
      'email.required'    => 'The email field is required.',
      'email.string'      => 'The email must be a string.',
      'email.email'       => 'The email must be a valid email address.',
      'email.max'         => 'The email must not be greater than 255 characters.',
      'email.unique'      => 'The email has already been taken.',
      'bio.string'        => 'The bio must be a string.',
      'password.string'   => 'The password must be a string.',
      'password.min'      => 'The password must be at least 8 characters.',
      'phone.string'      => 'The phone must be a string.',
      'phone.max'         => 'The phone must not be greater than 11 characters.',
      'image.image'       => 'The image must be an image.',
      'image.mimes'       => 'The image must be a file of type: jpeg, png, jpg, gif.',
    ];
  }
}
