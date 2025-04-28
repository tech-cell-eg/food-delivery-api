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
      'bio'       => 'nullable|string',
      'password'  => 'required|string|min:8',
      'phone'     => 'nullable|string|max:15',
      'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];
  }
}
