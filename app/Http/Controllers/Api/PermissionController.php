<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Services\ApiService;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Get all permissions",
     *     description="Retrieve a list of all permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of permissions")
     * )
     */
    public function index()
    {
        return ApiService::response(Permission::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Create a new permission",
     *     description="Create a permission",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="manage-users")
     *     )),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|unique:permissions,name']);
        $permission = Permission::create(['name' => $data['name']]);
        return ApiService::response($permission, 201);
    }

    /**
     * @OA\Put(
     *     path="/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Update a permission",
     *     description="Update a permission",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="edit-posts")
     *     )),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findById($id);
        $permission->update($request->only('name'));
        return ApiService::response($permission, 200);
    }

    /**
     * @OA\Delete(
     *     path="/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Delete a permission",
     *     description="Delete a permission",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $permission = Permission::findById($id);
        $permission->delete();
        return ApiService::response(['message' => 'Permission deleted'], 200);
    }
}
