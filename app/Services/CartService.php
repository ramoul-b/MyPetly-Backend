<?php

namespace App\Services;

use App\Models\CartItem;
use App\Services\OrderService;
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

    public function checkout(): \App\Models\Order
    {
        $items = CartItem::where('user_id', Auth::id())
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
        ];

        $order = $orderService->create($orderData);

        $this->clear();

        return $order;
    }
}
