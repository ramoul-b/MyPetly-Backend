<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProviderService;

class ProviderServicePolicy
{
    // Voir la personnalisation de son propre service
    public function view(User $user, ProviderService $ps): bool
    {
        return $user->can('view_own_provider_service') && $ps->provider_id === $user->provider->id;
    }

    // Modifier la personnalisation (prix, desc...) de son propre service
    public function update(User $user, ProviderService $ps): bool
    {
        return $user->can('edit_own_provider_service') && $ps->provider_id === $user->provider->id;
    }

    // Supprimer sa personnalisation
    public function delete(User $user, ProviderService $ps): bool
    {
        return $user->can('delete_own_provider_service') && $ps->provider_id === $user->provider->id;
    }
}
