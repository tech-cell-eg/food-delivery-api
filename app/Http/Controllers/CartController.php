<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartSyncRequest;
use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $cart = Cart::with(['items.meal.image', 'items.variant'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $this->successResponse([], 'Cart is empty', 404);
        }

        $items = CartItemResource::collection($cart->items);

        $total = $cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });

        return $this->successResponse([
            'items' => $items,
            'total' => $total
        ]);
    }

    public function syncCart(CartSyncRequest $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $newItems = collect($request->validated('cart_items'));

        $existingItems = CartItem::where('cart_id', $cart->id)
            ->get()
            ->keyBy(fn($item) => $item->meal_id . '_' . $item->meal_variant_id);

        $newKeys = $newItems->map(fn($item) => $item['meal_id'] . '_' . $item['meal_variant_id']);

        $deleteIds = $existingItems->diffKeys($newKeys->flip())->pluck('id');
        if ($deleteIds->isNotEmpty()) {
            CartItem::whereIn('id', $deleteIds)->delete();
        }

        $now = now();
        $updates = [];

        foreach ($newItems as $item) {
            $key = $item['meal_id'] . '_' . $item['meal_variant_id'];
            $quantity = $item['quantity'];



            if ($existing = $existingItems->get($key)) {
                if ($quantity <= 0) {
                    $existing->delete();
                } else if ($existing->quantity != $quantity) {
                    $updates[] = ['id' => $existing->id, 'quantity' => $quantity];
                }
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'meal_id' => $item['meal_id'],
                    'meal_variant_id' => $item['meal_variant_id'],
                    'quantity' => $quantity,
                ]);
            }
        }

        if (!empty($updates)) {
            $case = collect($updates)->map(fn($u) => "WHEN id = {$u['id']} THEN {$u['quantity']}")->join(' ');
            DB::update("
            UPDATE cart_items 
            SET quantity = CASE {$case} END,
                updated_at = '{$now->toDateTimeString()}'
            WHERE id IN (" . implode(',', array_column($updates, 'id')) . ")
        ");
        }

        return $this->successResponse(
            CartItemResource::collection(CartItem::where('cart_id', $cart->id)->get()),
            'Cart synced successfully'
        );
    }
}
