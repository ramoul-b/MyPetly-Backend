<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Animal;
use App\Models\Booking;

class AnimalPolicy
{
    /**
     * Determine whether the user can view any animals.
     */
    public function viewAny(User $user): bool
    {
        if ($user->can('view_any_animal')) {
            return true;
        }

        if ($user->hasRole('provider')) {
            $providerId = optional($user->provider)->id;
            return Booking::where('provider_id', $providerId)->exists();
        }

        return $user->can('view_own_animal');
    }

    /**
     * Determine whether the user can view a specific animal.
     */
    public function view(User $user, Animal $animal): bool
    {
        if ($user->can('view_any_animal')) {
            return true;
        }

        if ($user->hasRole('provider')) {
            $providerId = optional($user->provider)->id;
            return Booking::where('provider_id', $providerId)
                ->where('animal_id', $animal->id)
                ->exists();
        }

        if ($user->can('view_own_animal') && $animal->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_animal');
    }

    public function update(User $user, Animal $animal): bool
    {
        if ($user->can('edit_any_animal')) {
            return true;
        }

        if ($user->can('edit_own_animal') && $animal->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Animal $animal): bool
    {
        if ($user->can('delete_any_animal')) {
            return true;
        }

        if ($user->can('delete_own_animal') && $animal->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
