<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        if ($user->can('view_any_product')) {
            return true;
        }

        if (!$product->exists) {
            return $user->can('view_own_product');
        }

        return $user->can('view_own_product') && optional($product->store)->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_product');
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->can('edit_any_product')) {
            return true;
        }

        return $user->can('edit_own_product') && optional($product->store)->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->can('delete_any_product')) {
            return true;
        }

        return $user->can('delete_own_product') && optional($product->store)->user_id === $user->id;
    }
}
