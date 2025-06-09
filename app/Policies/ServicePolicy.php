<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function view(User $user, Service $service): bool
    {
        if ($user->can('view-services')) {
            return true;
        }

        if ($service->provider_id === $user->id) {

            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-services');

    }

    public function update(User $user, Service $service): bool
    {
        if ($user->can('edit-services')) {
            return true;
        }

        if ($service->provider_id === $user->id) {

            return true;
        }

        return false;
    }

    public function delete(User $user, Service $service): bool
    {
        if ($user->can('delete-services')) {
            return true;
        }

        if ($service->provider_id === $user->id) {

            return true;
        }

        return false;
    }
}
