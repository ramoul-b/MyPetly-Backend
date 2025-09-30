<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    public function checkout(array $data = []): Order
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $items = [];
        foreach ($cartItems as $cartItem) {
            $items[] = [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->product->price,
            ];
        }

        $orderData = array_merge($data, [
            'items' => $items,
        ]);

        $orderData['payment_status'] = $orderData['payment_status'] ?? 'paid';

        $order = $this->create($orderData);

        CartItem::where('cart_id', $cart->id)->delete();

        return $order;
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

    public function listForProvider(User $provider, array $filters): LengthAwarePaginator
    {
        $query = Order::with(['items', 'store']);

        $query->whereHas('store', function ($q) use ($provider) {
            $column = Schema::hasColumn('stores', 'provider_id') ? 'provider_id' : 'user_id';
            $q->where($column, $provider->id);
        });

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['items_count'])) {
            $query->withCount('items');
        }

        if (!empty($filters['sort'])) {
            $direction = 'asc';
            $field = $filters['sort'];
            if (str_starts_with($field, '-')) {
                $direction = 'desc';
                $field = substr($field, 1);
            }
            if ($field === 'date') {
                $query->orderBy('created_at', $direction);
            }
        }

        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        return $query->paginate($limit, ['*'], 'page', $page);
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

    public function updateShippingStatus(Order $order, string $status): Order
    {
        $order->shipping_status = $status;
        $order->save();

        return $order;
    }

    public function statsForProvider(User $provider, ?string $dateFrom, ?string $dateTo): array
    {
        $query = Order::query()->whereHas('store', function ($q) use ($provider) {
            $q->where('user_id', $provider->id);
        });

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $totalRevenue = (clone $query)->sum('total');
        $orderCount = (clone $query)->count();
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        return [
            'total_revenue' => $totalRevenue,
            'order_count' => $orderCount,
            'avg_order_value' => $avgOrderValue,
        ];
    }
}
