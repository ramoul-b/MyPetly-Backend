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
            'store',
            'store_manager',
            'store_collaborator',
            'user',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'sanctum']);
        }

        // 2. Définition des modules
        $modules = [
            'user', 'role', 'permission', 'animal', 'provider', 'service', 'provider_service', 'booking',
            'category', 'collar', 'review', 'payment'
        ];

        // 3. Permissions CRUD standards par module
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

        // 4. Permissions spécifiques supplémentaires
        $specialPermissions = [
            'attach_service_to_provider',
            'manage_payments',
            'assign_role',
            'manage_provider_services',
            // Permissions utilisées dans ProviderPolicy
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

        // Permissions par module pour chaque rôle
        $rolePermissions = [
            'super_admin' => Permission::pluck('name')->toArray(), // all permissions

            'admin' => array_merge(
                self::permissionsByAction(['view_any', 'create', 'edit_any', 'delete_any'], $modules),
                self::permissionsByAction(['assign_role', 'manage_payments', 'manage_provider_services'], []),
                ['approve-providers', 'view-providers'],
            ),

            'provider' => array_merge(
                self::permissionsByAction(['view_own', 'edit_own'], ['provider']),
                self::permissionsByAction(['view_any'], ['service', 'animal', 'category', 'collar', 'review']),
                self::permissionsByAction(['attach_service_to_provider', 'manage_provider_services', 'manage_payments'], []),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['provider_service']),
                self::permissionsByAction(['view_own', 'edit_own'], ['booking']),
                self::permissionsByAction(['view_own'], ['review'])
            ),

            'user' => array_merge(
                self::permissionsByAction(['view_own', 'edit_own'], ['user']),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['animal']),
                self::permissionsByAction(['view_any'], ['service', 'category', 'review', 'collar']),
                self::permissionsByAction(['view_own', 'create', 'edit_own', 'delete_own'], ['booking', 'review']),
                self::permissionsByAction(['manage_payments'], [])
            ),

            // Pour les rôles managers/collaborateurs, à compléter selon besoin business :
            'provider_manager' => [], // à compléter
            'provider_collaborator' => [], // à compléter
            'store' => [], // à compléter (pour phase 2 marketplace)
            'store_manager' => [],
            'store_collaborator' => [],
        ];

        // Attribution des permissions
        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            $role->syncPermissions($perms);
        }
    }

    /**
     * Génère les permissions par action et module.
     * @param array $actions
     * @param array $modules
     * @return array
     */
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
