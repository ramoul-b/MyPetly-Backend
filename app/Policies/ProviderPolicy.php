<?php

namespace App\Policies;

use App\Models\Provider;
use App\Models\User;

class ProviderPolicy
{
    public function view(User $user, Provider $provider): bool
    {
        if ($user->can('view-providers')) {
            return true;
        }

        if ($provider->id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('approve-providers');
    }

    public function update(User $user, Provider $provider): bool
    {
        if ($user->can('approve-providers')) {
            return true;
        }

        if ($provider->id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Provider $provider): bool
    {
        if ($user->can('approve-providers')) {
            return true;
        }

        if ($provider->id === $user->id) {
            return true;
        }

        return false;
    }
}
