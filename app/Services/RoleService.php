<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    public function createRole(array $data)
    {
        return Role::create($data);
    }

    public function updateRole($id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
    }

    public function assignPermissionsToRole($roleId, array $permissions)
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($permissions);
    }
}
