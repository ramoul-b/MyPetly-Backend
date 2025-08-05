<?php

namespace App\Policies;

use App\Models\ProductCategory;
use App\Models\User;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_product_category');
    }

    public function view(User $user, ProductCategory $category): bool
    {
        return $user->can('view_any_product_category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_product_category');
    }

    public function update(User $user, ProductCategory $category): bool
    {
        return $user->can('edit_any_product_category');
    }

    public function delete(User $user, ProductCategory $category): bool
    {
        return $user->can('delete_any_product_category');
    }
}
