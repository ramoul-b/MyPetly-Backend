<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function view(User $user, Review $review): bool
    {
        if ($user->can('view_any_review')) {
            return true;
        }

        if ($user->id === $review->user_id && $user->can('view_own_review')) {
            return true;
        }

        return $user->provider && $review->service && $review->service->provider_id === $user->provider->id && $user->can('view_own_review');
    }

    public function create(User $user): bool
    {
        return $user->can('create_review');
    }

    public function update(User $user, Review $review): bool
    {
        if ($user->can('edit_any_review')) {
            return true;
        }

        return $user->id === $review->user_id && $user->can('edit_own_review');
    }

    public function delete(User $user, Review $review): bool
    {
        if ($user->can('delete_any_review')) {
            return true;
        }

        return $user->id === $review->user_id && $user->can('delete_own_review');
    }
}
