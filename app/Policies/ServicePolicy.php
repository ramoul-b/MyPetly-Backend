<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

class ServicePolicy
{
    // Voir un service (tout le monde peut voir)
    public function view(User $user, Service $service): bool
    {
        return true;
    }

    // Créer un service (réservé à admin/super_admin)
    public function create(User $user): bool
    {
        return $user->can('create_service');
    }

    // Modifier un service (réservé à admin/super_admin)
    public function update(User $user, Service $service): bool
    {
        return $user->can('edit_any_service');
    }

    // Supprimer un service (réservé à admin/super_admin)
    public function delete(User $user, Service $service): bool
    {
        return $user->can('delete_any_service');
    }

    // Attribuer un service à un provider (action spéciale, ex: via pivot)
    public function attachToProvider(User $user, Service $service): bool
    {
        // Le provider peut s’attribuer un service existant (provider_service)
        return $user->can('attach_service_to_provider');
    }
}
