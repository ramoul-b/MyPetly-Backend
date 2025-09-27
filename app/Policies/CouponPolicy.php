<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function view(User $user, Coupon $coupon): bool
    {
        if ($user->can('view_any_coupon')) {
            return true;
        }

        if (!$coupon->exists) {
            return $user->can('view_own_coupon');
        }

        return $user->can('view_own_coupon') && optional($coupon->store)->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_coupon');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        if ($user->can('edit_any_coupon')) {
            return true;
        }

        return $user->can('edit_own_coupon') && optional($coupon->store)->user_id === $user->id;
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        if ($user->can('delete_any_coupon')) {
            return true;
        }

        return $user->can('delete_own_coupon') && optional($coupon->store)->user_id === $user->id;
    }
}
