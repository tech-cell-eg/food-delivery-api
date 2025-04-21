<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemUpdateRequest;
use App\Http\Requests\CartSyncRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $cart = Cart::with(['items.meal', 'items.variant'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $this->errorResponse('Cart is empty', 404);
        }

        $items = [];
        $total = 0;

        foreach ($cart->items as $item) {
            $mealPrice = $item->variant->price;
            $itemTotal = $mealPrice * $item->quantity;
            $total += $itemTotal;

            $items[] = [
                'cart_item_id' => $item->id,
                'meal_name' => $item->meal->name,
                'variant' => $item->variant->name,
                'quantity' => $item->quantity,
                'meal_price' => $mealPrice,
                'item_total' => $itemTotal
            ];
        }

        return $this->successResponse([
            'items' => $items,
            'total' => $total
        ]);
    }

    public function store(CartSyncRequest $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $items = array_map(fn($item) => [
            'meal_id' => $item['meal_id'],
            'meal_variant_id' => $item['meal_variant_id'],
            'quantity' => $item['quantity'],
            'cart_id' => $cart->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $request->validated('cart_items'));

        CartItem::insert($items);

        return $this->successResponse('Items added to cart successfully');
    }

    public function updateItem(CartItemUpdateRequest $request, $id)
    {
        $cartItem = CartItem::whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))->find($id);

        if (!$cartItem) {
            return $this->errorResponse('Cart item not found', 404);
        }

        $cartItem->update(['quantity' => $request->validated('quantity')]);

        return $this->successResponse('Cart item updated successfully');
    }

    public function destroy($id)
    {
        $cartItem = CartItem::whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))->find($id);

        if (!$cartItem) {
            return $this->errorResponse('Cart item not found', 404);
        }

        $cartItem->delete();

        return $this->successResponse('Cart item removed successfully');
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

            if ($quantity <= 0) continue;

            if ($existing = $existingItems->get($key)) {
                if ($existing->quantity != $quantity) {
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

        return $this->successResponse('Cart synced successfully');
    }
}
