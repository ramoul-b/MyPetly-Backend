<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Animal;

class AnimalPolicy
{
    public function view(User $user, Animal $animal): bool
    {
        if ($user->can('view-animals')) {
            return true;
        }

        if ($animal->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-animals');
    }

    public function update(User $user, Animal $animal): bool
    {
        if ($user->can('edit-animals')) {
            return true;
        }

        if ($animal->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Animal $animal): bool
    {
        if ($user->can('delete-animals')) {
            return true;
        }

        if ($animal->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
