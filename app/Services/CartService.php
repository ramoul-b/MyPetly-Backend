<?php

namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function addToCart(array $data): CartItem
    {
        return CartItem::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $data['product_id'],
            ],
            [
                'quantity' => $data['quantity']
            ]
        );
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        CartItem::where('user_id', Auth::id())->delete();
    }
}
