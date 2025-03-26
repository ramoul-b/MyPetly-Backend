<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = new Order();
            $order->user_id = Auth::id();
            $order->total = 0;
            $order->status = 'pending';
            $order->save();

            $total = 0;

            foreach ($data['items'] as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $item['price'];
                $orderItem->save();

                $total += $item['price'] * $item['quantity'];
            }

            $order->total = $total;
            $order->save();

            return $order;
        });
    }
}
