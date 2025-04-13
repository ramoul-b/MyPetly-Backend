<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
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
        try {
            $roles = Role::all();
            return ApiService::response($roles, 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
    public function store(StoreRoleRequest $request)
    {
        try {
            $role = Role::create(['name' => $request->name]);
            return ApiService::response($role, 201);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = Role::findById($id);
            $role->update(['name' => $request->name]);
            return ApiService::response($role, 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
        try {
            $role = Role::findById($id);
            $role->delete();
            return ApiService::response(['message' => 'Role deleted'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
        try {
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            $role = Role::findById($id);
            $role->syncPermissions($request->permissions);

            return ApiService::response(['message' => 'Permissions updated'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
        try {
            $role = Role::findById($id);
            return ApiService::response($role->permissions, 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
    }
}
