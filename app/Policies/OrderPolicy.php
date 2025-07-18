<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($user->can('view_any_order')) {
            return true;
        }

        if (!$order->exists) {
            return $user->can('view_own_order');
        }

        if ($user->can('view_own_order')) {
            if ($order->user_id === $user->id) {
                return true;
            }
            return $order->store && $order->store->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_order');
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->can('edit_any_order')) {
            return true;
        }

        if ($user->can('edit_own_order')) {
            if ($order->user_id === $user->id) {
                return true;
            }

            return $order->store && $order->store->user_id === $user->id;
        }

        return false;
    }
}
