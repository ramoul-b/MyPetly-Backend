<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_user');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id && $user->can('view_own_user')) {
            return true;
        }

        return $user->can('view_any_user');
    }

    public function create(User $user): bool
    {
        return $user->can('create_user');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->can('edit_any_user')) {
            return true;
        }

        return $user->id === $model->id && $user->can('edit_own_user');
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->can('delete_any_user')) {
            return true;
        }

        return $user->id === $model->id && $user->can('delete_own_user');
    }
}
