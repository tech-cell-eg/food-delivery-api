<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AddressResource;
use App\Models\Address;

class AddressController extends Controller
{
    use ApiResponse;
    public function store(StoreAddressRequest $request)
    {
        if ($request->is_default) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($request->validated());

        return $this->successResponse(new AddressResource($address), 'Address created successfully', 201);
    }

    public function index()
    {
        $addresses = Auth::user()->addresses;

        return $this->successResponse(AddressResource::collection($addresses), 'Addresses retrieved successfully');
    }

    public function show(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return $this->errorResponse('You do not have access', 404);
        }

        return $this->successResponse(new AddressResource($address), 'Address retrieved successfully');
    }
}
