<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\ApiService;

class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Get all roles",
     *     description="Retrieve a list of all roles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of roles")
     * )
     */
    public function index()
    {
        return ApiService::response(Role::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Create a new role",
     *     description="Create a role",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="admin")
     *     )),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|unique:roles,name']);
        $role = Role::create(['name' => $data['name']]);
        return ApiService::response($role, 201);
    }

    /**
     * @OA\Put(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Update a role",
     *     description="Update a role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="editor")
     *     )),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $role = Role::findById($id);
        $role->update($request->only('name'));
        return ApiService::response($role, 200);
    }

    /**
     * @OA\Delete(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Delete a role",
     *     description="Delete a role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $role = Role::findById($id);
        $role->delete();
        return ApiService::response(['message' => 'Role deleted'], 200);
    }

    /**
     * @OA\Post(
     *     path="/roles/{id}/permissions",
     *     tags={"Roles"},
     *     summary="Assign permissions to role",
     *     description="Attach permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
     *     )),
     *     @OA\Response(response=200, description="Assigned")
     * )
     */
    public function assignPermissions(Request $request, $id)
    {
        $role = Role::findById($id);
        $role->syncPermissions($request->permissions);
        return ApiService::response(['message' => 'Permissions updated'], 200);
    }

    /**
     * @OA\Get(
     *     path="/roles/{id}/permissions",
     *     tags={"Roles"},
     *     summary="List permissions for role",
     *     description="List permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Permissions list")
     * )
     */
    public function listPermissions($id)
    {
        $role = Role::findById($id);
        return ApiService::response($role->permissions, 200);
    }
}

