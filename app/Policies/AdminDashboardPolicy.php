<?php

namespace App\Policies;

use App\Models\User;

class AdminDashboardPolicy
{
    public function view(User $user): bool
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        return $user->can('view_admin_dashboard');
    }
}
