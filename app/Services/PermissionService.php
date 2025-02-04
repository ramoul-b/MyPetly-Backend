<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function createPermission(array $data)
    {
        return Permission::create($data);
    }

    public function updatePermission($id, array $data)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);
        return $permission;
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
    }
}
