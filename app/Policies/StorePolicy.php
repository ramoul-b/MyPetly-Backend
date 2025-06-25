<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function view(User $user, Store $store): bool
    {
        if ($user->can('view_any_store')) {
            return true;
        }

        if ($user->can('view_own_store') && optional($store->provider)->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_store');
    }

    public function update(User $user, Store $store): bool
    {
        if ($user->can('edit_any_store')) {
            return true;
        }

        if ($user->can('edit_own_store') && optional($store->provider)->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Store $store): bool
    {
        if ($user->can('delete_any_store')) {
            return true;
        }

        if ($user->can('delete_own_store') && optional($store->provider)->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
