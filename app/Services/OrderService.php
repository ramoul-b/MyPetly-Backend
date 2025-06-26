<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\CartItem;
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
            $order->payment_status = $data['payment_status'] ?? 'pending';
            $order->shipping_status = $data['shipping_status'] ?? 'pending';
            $order->shipping_address = $data['shipping_address'] ?? null;
            $order->billing_address = $data['billing_address'] ?? null;
            // Determine store from first product if not provided
            $firstProduct = Product::find($data['items'][0]['product_id']);
            $order->store_id = $firstProduct?->store_id ?? $data['store_id'] ?? null;
            $order->save();

            $total = 0;

            foreach ($data['items'] as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $item['unit_price'];
                $orderItem->save();

                $total += $item['unit_price'] * $item['quantity'];
            }

            $order->total = $total;
            $order->save();

            return $order;
        });
    }

    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        $query = Order::with(['items.product', 'store']);
        $user = Auth::user();

        if ($user->can('view_any_order')) {
            return $query->get();
        }

        if ($user->can('view_own_order') && $user->provider) {
            $query->whereHas('store', function ($q) use ($user) {
                $q->where('provider_id', $user->provider->id);
            });
            return $query->get();
        }

        return collect();
    }

    public function find(int $id): Order
    {
        $query = Order::with(['items.product', 'store'])->where('id', $id);
        $user = Auth::user();

        if ($user->can('view_any_order')) {
            return $query->firstOrFail();
        }

        if ($user->can('view_own_order') && $user->provider) {
            $query->whereHas('store', function ($q) use ($user) {
                $q->where('provider_id', $user->provider->id);
            });
            return $query->firstOrFail();
        }

        abort(403, 'Unauthorized');
    }

    /**
     * Finalise the current user's cart into orders by store.
     */
    public function checkout(): \Illuminate\Support\Collection
    {
        return DB::transaction(function () {
            $userId = Auth::id();
            $cartItems = CartItem::with('product')->where('user_id', $userId)->get();

            $grouped = $cartItems->groupBy(fn ($item) => $item->product?->store_id);
            $orders = collect();

            foreach ($grouped as $storeId => $items) {
                $order = new Order();
                $order->user_id = $userId;
                $order->store_id = $storeId;
                $order->total = 0;
                $order->status = 'pending';
                $order->payment_status = 'paid';
                $order->shipping_status = 'pending';
                $order->save();

                $total = 0;

                foreach ($items as $cartItem) {
                    $product = $cartItem->product;
                    if (!$product) {
                        continue;
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $product->price,
                    ]);

                    $product->decrement('stock', $cartItem->quantity);
                    $total += $product->price * $cartItem->quantity;
                }

                $order->total = $total;
                $order->save();
                $orders->push($order);
            }

            CartItem::where('user_id', $userId)->delete();

            return $orders->load(['items.product', 'store']);
        });
    }
}
