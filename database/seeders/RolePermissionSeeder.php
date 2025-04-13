<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Création des permissions
        $permissions = [
            'view users',
            'edit users',
            'delete users',
            'manage services',
            'view bookings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Création des rôles et assignation des permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $provider = Role::firstOrCreate(['name' => 'provider']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $admin->syncPermissions(Permission::all()); // Tous les droits
        $provider->syncPermissions(['manage services', 'view bookings']);
        $user->syncPermissions([]); // Aucun par défaut
    }
}
