<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function view(User $user, Service $service): bool
    {
        if ($user->can('view_any_service')) {
            return true;
        }

        if ($user->can('view_own_service') && $service->provider_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_service');
    }

    public function update(User $user, Service $service): bool
    {
        if ($user->can('edit_any_service')) {
            return true;
        }

        if ($user->can('edit_own_service') && $service->provider_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Service $service): bool
    {
        if ($user->can('delete_any_service')) {
            return true;
        }

        if ($user->can('delete_own_service') && $service->provider_id === $user->id) {
            return true;
        }

        return false;
    }
}
