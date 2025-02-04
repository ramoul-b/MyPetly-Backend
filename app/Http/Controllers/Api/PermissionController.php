<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use App\Services\ApiService;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * @OA\Get(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Get all permissions",
     *     description="Retrieve a list of all permissions",
     *     @OA\Response(
     *         response=200,
     *         description="List of permissions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Manage Users"),
     *                 @OA\Property(property="slug", type="string", example="manage-users"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:15:37Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:15:37Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $permissions = $this->permissionService->getAllPermissions();
            return ApiService::response(PermissionResource::collection($permissions), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Create a new permission",
     *     description="Add a new permission to the system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", example="Manage Users"),
     *             @OA\Property(property="slug", type="string", example="manage-users")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Permission created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Manage Users"),
     *             @OA\Property(property="slug", type="string", example="manage-users"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:15:37Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:15:37Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = $this->permissionService->createPermission($request->validated());
            return ApiService::response(new PermissionResource($permission), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Update a permission",
     *     description="Update an existing permission",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the permission to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", example="Edit Posts"),
     *             @OA\Property(property="slug", type="string", example="edit-posts")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Edit Posts"),
     *             @OA\Property(property="slug", type="string", example="edit-posts"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:15:37Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:15:37Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permission not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        try {
            $permission = $this->permissionService->updatePermission($id, $request->validated());
            return ApiService::response(new PermissionResource($permission), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Delete a permission",
     *     description="Remove a permission from the system",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permission deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permission not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->permissionService->deletePermission($id);
            return ApiService::response(['message' => __('messages.permission_deleted')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }
}
