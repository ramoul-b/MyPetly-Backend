<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\OrderService;
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

    public function checkout(?string $shippingAddress = null, ?string $billingAddress = null): \App\Models\Order
    {
        $cart = $this->getUserCart();

        $items = $cart->items()
            ->with('product')
            ->get();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Cart is empty');
        }

        $orderService = app(OrderService::class);

        $orderData = [
            'items' => $items->map(fn($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price,
            ])->toArray(),
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
        ];

        $order = $orderService->create($orderData);

        $this->clear();

        return $order;
    }
}
