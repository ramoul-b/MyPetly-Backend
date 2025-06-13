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
    public function updateUser(int $id, array $data)
{
    $user = User::findOrFail($id);

    // Photo
    if (isset($data['photo'])) {
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $data['photo'] = $data['photo']->store('profiles', 'public');
    }

    $user->update($data);

    // retourner avec rôles + permissions
    return $user->load(['roles', 'permissions']);
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

    public function assignRoles($userId, array $roles)
    {
        $user = User::findOrFail($userId);
        $user->syncRoles($roles);
        return $user->roles;
    }

    public function revokeRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->removeRole($role);
        return true;
    }

    public function getRoles($userId)
    {
        $user = User::findOrFail($userId);
        return $user->roles;
    }

    public function getPermissions($userId)
    {
        $user = User::findOrFail($userId);
        return $user->permissions;
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        return $user->delete();
    }

    public function searchUsers(string $query)
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }

    public function getAllUsers(int $perPage = 15)
    {
        return User::with(['roles', 'permissions'])->paginate($perPage);
    }
    
}
