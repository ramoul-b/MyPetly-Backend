<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyage
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Liste des permissions
        $permissions = [
            // Animaux
            'view-animals', 'create-animals', 'edit-animals', 'delete-animals',
            'attach-collar', 'mark-as-lost', 'mark-as-found',

            // Colliers
            'view-collars', 'create-collars', 'edit-collars', 'delete-collars', 'scan-collar',

            // Utilisateurs
            'view-users', 'create-users', 'edit-users', 'delete-users', 'assign-role',

            // Rôles & permissions
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            'manage-roles', 'view-permissions', 'assign-permissions',

            // Services & prestataires
            'view-services', 'create-services', 'edit-services', 'delete-services',
            'view-providers', 'approve-providers',

            // Produits & commandes
            'view-products', 'create-products', 'edit-products', 'delete-products',
            'view-orders', 'manage-orders',

            // Avis
            'view-reviews', 'moderate-reviews',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Création des rôles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'sanctum']);
        $provider = Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'sanctum']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);

        // Attribution des permissions par rôle

        // 🔐 Admin = tout
        $admin->syncPermissions(Permission::all());

        // 👤 User
        $user->syncPermissions([
            'view-animals', 'create-animals', 'edit-animals', 'delete-animals',
            'attach-collar', 'mark-as-lost', 'mark-as-found',
            'view-products', 'view-orders', 'view-reviews',
        ]);

        // 🧑‍🔧 Provider
        $provider->syncPermissions([
            'view-services', 'create-services', 'edit-services', 'delete-services',
            'view-animals', 'view-orders', 'view-reviews',
        ]);

        // 📊 Manager
        $manager->syncPermissions([
            'view-animals', 'edit-animals', 'view-users', 'view-orders',
            'view-services', 'approve-providers',
            'view-reviews', 'moderate-reviews'
        ]);
    }
}