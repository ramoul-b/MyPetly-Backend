<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Enregistrer un nouvel utilisateur avec une photo.
     */
    public function createUser(array $data)
{
    if (isset($data['photo'])) {
        $data['photo'] = $data['photo']->store('profiles', 'public');
    }

    return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'photo' => $data['photo'] ?? null,
    ]);
}

    /**
     * Mettre à jour le profil utilisateur, y compris la photo.
     */
    public function updateUser(User $user, array $data)
{
    if (isset($data['photo'])) {
        // Supprimer l'ancienne photo si elle existe
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        // Sauvegarder la nouvelle photo
        $data['photo'] = $data['photo']->store('profiles', 'public');
    }

    // Mise à jour des informations
    $user->update($data);

    return $user;
}


    /**
     * Supprimer une ancienne photo avant d’en enregistrer une nouvelle.
     */
    private function deleteOldPhoto(User $user)
    {
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
    }

    /**
     * Gérer l'upload d'une nouvelle photo de profil.
     */
    private function uploadPhoto($photo)
    {
        return $photo->store('profiles', 'public');
    }

    public function findUserById($id)
    {
        return User::findOrFail($id);
    }
    
    public function assignRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
    
        $user->assignRole($role->name);
    
        return true;
    }
    
}
