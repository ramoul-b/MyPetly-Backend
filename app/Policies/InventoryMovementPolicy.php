<?php

namespace App\Policies;

use App\Models\InventoryMovement;
use App\Models\User;

class InventoryMovementPolicy
{
    public function view(User $user, InventoryMovement $movement): bool
    {
        if ($user->can('view_any_inventory_movement')) {
            return true;
        }

        if (!$movement->exists) {
            return $user->can('view_own_inventory_movement');
        }

        return $user->can('view_own_inventory_movement') && optional($movement->store)->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_inventory_movement');
    }

    public function update(User $user, InventoryMovement $movement): bool
    {
        if ($user->can('edit_any_inventory_movement')) {
            return true;
        }

        return $user->can('edit_own_inventory_movement') && optional($movement->store)->user_id === $user->id;
    }

    public function delete(User $user, InventoryMovement $movement): bool
    {
        if ($user->can('delete_any_inventory_movement')) {
            return true;
        }

        return $user->can('delete_own_inventory_movement') && optional($movement->store)->user_id === $user->id;
    }
}
