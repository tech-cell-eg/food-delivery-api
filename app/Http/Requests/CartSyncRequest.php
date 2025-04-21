<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartSyncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'cart_items' => 'required|array',
            'cart_items.*.meal_id' => 'required|exists:meals,id',
            'cart_items.*.meal_variant_id' => 'required|exists:meal_variants,id',
            'cart_items.*.quantity' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'cart_items.*.meal_id.required' => 'The meal ID is required.',
            'cart_items.*.meal_variant_id.required' => 'The meal variant ID is required.',
            'cart_items.*.quantity.required' => 'The quantity is required.',
            'cart_items.*.quantity.integer' => 'The quantity must be an integer.',
        ];
    }
}
