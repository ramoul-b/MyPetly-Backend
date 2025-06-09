<?php

namespace App\Policies;

use App\Models\Provider;
use App\Models\User;

class ProviderPolicy
{
    public function view(User $user, Provider $provider): bool
    {
        if ($user->can('view_any_provider')) {
            return true;
        }

        if ($user->can('view_own_provider') && $provider->id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_provider');
    }

    public function update(User $user, Provider $provider): bool
    {
        if ($user->can('edit_any_provider')) {
            return true;
        }

        if ($user->can('edit_own_provider') && $provider->id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Provider $provider): bool
    {
        if ($user->can('delete_any_provider')) {
            return true;
        }

        if ($user->can('delete_own_provider') && $provider->id === $user->id) {
            return true;
        }

        return false;
    }
}
