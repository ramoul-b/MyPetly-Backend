<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getUserCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    public function addToCart(array $data): CartItem
    {
        $cart = $this->getUserCart();

        return CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $data['product_id'],
            ],
            [
                'quantity' => $data['quantity'],
            ]
        );
    }

    public function updateItemQuantity(CartItem $item, int $qty): CartItem
    {
        $item->quantity = $qty;
        $item->save();

        return $item;
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        $cart = $this->getUserCart();
        $cart->items()->delete();
    }

    public function listItems()
    {
        $cart = $this->getUserCart();

        return $cart->items()->with('product')->get();
    }
}
