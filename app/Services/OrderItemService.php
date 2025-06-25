<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class OrderItemService
{
    public function listByOrder(Order $order): Collection
    {
        return $order->items()->with('product')->get();
    }

    public function find(int $id): OrderItem
    {
        return OrderItem::with(['product', 'order.store'])->findOrFail($id);
    }
}
