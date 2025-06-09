<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->can('view-orders')) {
            return true;
        }

        if ($booking->user_id === $user->id || $booking->provider_id === $user->id) {

            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('manage-orders');

    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->can('manage-orders')) {
            return true;
        }

        if ($booking->user_id === $user->id || $booking->provider_id === $user->id) {

            return true;
        }

        return false;
    }

    public function delete(User $user, Booking $booking): bool
    {
        if ($user->can('manage-orders')) {
            return true;
        }

        if ($booking->user_id === $user->id || $booking->provider_id === $user->id) {

            return true;
        }

        return false;
    }
}
