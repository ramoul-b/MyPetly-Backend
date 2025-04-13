<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
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
        try {
            $permissions = Permission::all();
            return ApiService::response($permissions, 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Create a new permission",
     *     description="Create a permission",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="view-users")
     *     )),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = Permission::create(['name' => $request->name]);
            return ApiService::response($permission, 201);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
     *         @OA\Property(property="name", type="string", example="edit-users")
     *     )),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        try {
            $permission = Permission::findById($id);
            $permission->update(['name' => $request->name]);
            return ApiService::response($permission, 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
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
        try {
            $permission = Permission::findById($id);
            $permission->delete();
            return ApiService::response(['message' => 'Permission deleted'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Erreur serveur.'], 500);
        }
    }
}
