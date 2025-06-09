<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->can('view_any_booking')) {
            return true;
        }

        if ($user->can('view_own_booking') && ($booking->user_id === $user->id || $booking->provider_id === $user->id)) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_booking');
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->can('edit_any_booking')) {
            return true;
        }

        if ($user->can('edit_own_booking') && ($booking->user_id === $user->id || $booking->provider_id === $user->id)) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Booking $booking): bool
    {
        if ($user->can('delete_any_booking')) {
            return true;
        }

        if ($user->can('delete_own_booking') && ($booking->user_id === $user->id || $booking->provider_id === $user->id)) {
            return true;
        }

        return false;
    }
}
