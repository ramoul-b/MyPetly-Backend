<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function view(User $user, Category $category): bool
    {
        return $user->can('view-services');

    }

    public function create(User $user): bool
    {
        return $user->can('create-services');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('edit-services');

    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('delete-services');
    }
}
