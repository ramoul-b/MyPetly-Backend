<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;

class CartItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CartItem $item): bool
    {
        return optional($item->cart)->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CartItem $item): bool
    {
        return optional($item->cart)->user_id === $user->id;
    }

    public function delete(User $user, CartItem $item): bool
    {
        return optional($item->cart)->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->id !== null;
    }
}
