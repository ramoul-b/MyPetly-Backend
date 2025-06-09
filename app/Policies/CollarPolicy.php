<?php

namespace App\Policies;

use App\Models\Collar;
use App\Models\User;

class CollarPolicy
{
    public function view(User $user, Collar $collar): bool
    {
        if ($user->can('view_any_collar')) {
            return true;
        }

        if ($user->can('view_own_collar') && optional($collar->animal)->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_collar');
    }

    public function update(User $user, Collar $collar): bool
    {
        if ($user->can('edit_any_collar')) {
            return true;
        }

        if ($user->can('edit_own_collar') && optional($collar->animal)->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Collar $collar): bool
    {
        if ($user->can('delete_any_collar')) {
            return true;
        }

        if ($user->can('delete_own_collar') && optional($collar->animal)->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
