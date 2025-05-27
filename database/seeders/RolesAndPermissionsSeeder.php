<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /* ── 0. purger uniquement les tables RBAC ─────────────────────────── */
        Schema::disableForeignKeyConstraints();

        // pivots d’abord
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();

        // tables maîtres
        Role::truncate();
        Permission::truncate();

        Schema::enableForeignKeyConstraints();

        /* ── 1. vider le cache spatie ──────────────────────────────────────── */
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /* ── 2. permissions ────────────────────────────────────────────────── */
    $permissions = [
    // wildcards maîtres
    'account.*','users.*','pets.*','products.*','orders.*',
    'services.*','bookings.*','moderation.*','dashboard.*','orders.create','orders.view',

    // granularité fine
    'account.view_self','account.update_self',
    'users.view','users.create','users.update','users.delete',
    'pets.create','pets.view','pets.update','pets.delete',
    ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'sanctum']);
        }

        /* ── 3. rôles + mapping ───────────────────────────────────────────── */
        $roles = [
            'super_admin' => Permission::all()->pluck('name'),
            'admin'       => ['users.*','products.*','orders.*','moderation.*','dashboard.*'],
            'user'        => ['account.*','pets.*','orders.create','orders.view'],
        ];
        foreach ($roles as $name => $perms) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'sanctum']);
            $role->syncPermissions($perms);
        }

        /* ── 4. premier user → super_admin ────────────────────────────────── */
        User::first()?->assignRole('super_admin');
    }
}
