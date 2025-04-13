<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    public function getAllRoles()
    {
        return Role::all();
    }

    public function findRoleById($id)
    {
        return Role::findOrFail($id);
    }

    public function createRole(array $data)
    {
        return Role::create(['name' => $data['name']]);
    }

    public function updateRole($id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $data['name']]);
        return $role;
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }

    public function assignPermissions($roleId, array $permissions)
    {
        $role = Role::findOrFail($roleId);
        return $role->syncPermissions($permissions);
    }

    public function getPermissions($roleId)
    {
        $role = Role::findOrFail($roleId);
        return $role->permissions;
    }

    public function revokePermission($roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $permission = Permission::findOrFail($permissionId);
        return $role->revokePermissionTo($permission);
    }
}
