<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Création des rôles
        $roles = [
            'super_admin',
            'admin',
            'provider',
            'provider_manager',
            'provider_collaborator',
            'user',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'sanctum']);
        }

        // 2. Modules disponibles
        $modules = [
            'user', 'role', 'permission', 'animal', 'provider',
            'service', 'provider_service', 'booking', 'category',
            'collar', 'review', 'payment', 'product_category', 'store',
            'product', 'order', 'order_item'
        ];

        // 3. Actions CRUD par module
        $actions = [
            'view_any', 'view_own', 'create', 'edit_any', 'edit_own', 'delete_any', 'delete_own'
        ];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$module}",
                    'guard_name' => 'sanctum'
                ]);
            }
        }

        // 4. Permissions spécifiques
        $specialPermissions = [
            'attach_service_to_provider',
            'manage_payments',
            'assign_role',
            'manage_provider_services',
            'approve-providers',
            'view-providers'
        ];

        foreach ($specialPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'sanctum'
            ]);
        }

        // 5. Attribution des permissions aux rôles
        $rolePermissions = [
            'super_admin' => Permission::pluck('name')->toArray(),

            'admin' => array_merge(
                self::permissionsByAction(['view_any', 'create', 'edit_any', 'delete_any'], $modules),
                self::permissionsByAction(['assign_role', 'manage_payments', 'manage_provider_services'], []),
                ['approve-providers', 'view-providers']
            ),

            'provider' => array_merge(
                self::permissionsByAction(['view_own', 'edit_own'], ['provider']),
                self::permissionsByAction(['view_any'], ['service', 'category', 'product_category', 'collar', 'review']),
                self::permissionsByAction(['attach_service_to_provider', 'manage_provider_services', 'manage_payments'], []),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['provider_service']),
                self::permissionsByAction(['view_own', 'edit_own'], ['booking']),
                self::permissionsByAction(['view_own'], ['review']),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['store', 'product']),
                self::permissionsByAction(['view_own', 'edit_own'], ['order'])
            ),

            'user' => array_merge(
                self::permissionsByAction(['view_own', 'edit_own'], ['user']),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['animal']),
                self::permissionsByAction(['view_any'], ['service', 'category', 'product_category', 'review', 'collar']),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['booking', 'review']),
                self::permissionsByAction(['manage_payments'], [])
            ),

            'provider_manager' => [],
            'provider_collaborator' => [],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            $role->syncPermissions($perms);
        }
    }

    private static function permissionsByAction(array $actions, array $modules)
    {
        $perms = [];
        foreach ($actions as $action) {
            if ($modules) {
                foreach ($modules as $module) {
                    $perms[] = "{$action}_{$module}";
                }
            } else {
                $perms[] = $action;
            }
        }
        return $perms;
    }
}
