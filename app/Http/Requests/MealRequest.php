<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          'name' => 'required|string|max:255',
          'description' => 'nullable|string|max:1000',
          'rate' => 'nullable|numeric|min:0|max:5',
          'staurant_id' => 'required|exists:restaurants,id',
          'category_id' => 'required|exists:categories,id',
          'is_available' => 'required|boolean',
          'chief_id' => 'required|exists:chiefs,id',
          

            //
        ];
    }
}
