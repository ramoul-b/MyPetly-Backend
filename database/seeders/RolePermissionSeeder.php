<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Création des rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'sanctum']);

        // Permissions (liste à factoriser)
        $permissions = [
            'view-animals', 'create-animals', 'edit-animals', 'delete-animals',
            'view-users', 'create-users', 'edit-users', 'delete-users',
            'manage-roles', 'manage-permissions'
        ];

        // Création des permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'sanctum']);
        }

        // Attribution des permissions aux rôles
        $adminRole->syncPermissions($permissions);

        $managerRole->syncPermissions([
            'view-animals', 'create-animals', 'edit-animals',
            'view-users'
        ]);

        $userRole->syncPermissions([
            'view-animals', 'create-animals', 'edit-animals', 'delete-animals'
        ]);
    }
}
