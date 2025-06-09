<?php

namespace App\Policies;

use App\Models\Collar;
use App\Models\User;

class CollarPolicy
{
    public function view(User $user, Collar $collar): bool
    {
        if ($user->can('view-collars')) {
            return true;
        }

        if (optional($collar->animal)->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-collars');
    }

    public function update(User $user, Collar $collar): bool
    {
        if ($user->can('edit-collars')) {
            return true;
        }

        if (optional($collar->animal)->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Collar $collar): bool
    {
        if ($user->can('delete-collars')) {
            return true;
        }

        if (optional($collar->animal)->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
