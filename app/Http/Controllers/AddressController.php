<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AddressResource;

class AddressController extends Controller
{
    use ApiResponse;
    public function store(Request $request)
    {
        $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'is_default' => ['boolean'],
        ]);

        if ($request->is_default) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create([
            'label' => $request->label,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'is_default' => $request->is_default ?? false,
        ]);

        return $this->successResponse(new AddressResource($address), 'Address created successfully', 201);
    }

    public function index()
    {
        $addresses = Auth::user()->addresses;

        return $this->successResponse(AddressResource::collection($addresses), 'Addresses retrieved successfully');
    }

    public function show($id)
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return $this->errorResponse('Address not found', 404);
        }

        return $this->successResponse(new AddressResource($address), 'Address retrieved successfully');
    }
}
