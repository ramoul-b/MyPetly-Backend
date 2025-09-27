<?php

namespace App\Policies;

use App\Models\StoreSetting;
use App\Models\User;

class StoreSettingPolicy
{
    public function view(User $user, StoreSetting $setting): bool
    {
        if ($user->can('view_any_store_setting')) {
            return true;
        }

        if (!$setting->exists) {
            return $user->can('view_own_store_setting');
        }

        return $user->can('view_own_store_setting') && optional($setting->store)->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_store_setting');
    }

    public function update(User $user, StoreSetting $setting): bool
    {
        if ($user->can('edit_any_store_setting')) {
            return true;
        }

        return $user->can('edit_own_store_setting') && optional($setting->store)->user_id === $user->id;
    }

    public function delete(User $user, StoreSetting $setting): bool
    {
        if ($user->can('delete_any_store_setting')) {
            return true;
        }

        return $user->can('delete_own_store_setting') && optional($setting->store)->user_id === $user->id;
    }
}
