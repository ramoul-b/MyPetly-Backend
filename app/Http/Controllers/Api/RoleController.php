<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Services\ApiService;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @OA\Get(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Get all roles",
     *     description="Retrieve a list of all roles",
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Admin"),
     *                 @OA\Property(property="slug", type="string", example="admin"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:25:55Z")
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
            $roles = $this->roleService->getAllRoles();
            return ApiService::response(RoleResource::collection($roles), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Create a new role",
     *     description="Add a new role to the system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", example="Editor"),
     *             @OA\Property(property="slug", type="string", example="editor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=2),
     *             @OA\Property(property="name", type="string", example="Editor"),
     *             @OA\Property(property="slug", type="string", example="editor"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:25:55Z")
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
    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->roleService->createRole($request->validated());
            return ApiService::response(new RoleResource($role), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Update a role",
     *     description="Update the details of an existing role",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", example="Editor"),
     *             @OA\Property(property="slug", type="string", example="editor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=2),
     *             @OA\Property(property="name", type="string", example="Editor"),
     *             @OA\Property(property="slug", type="string", example="editor"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:25:55Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role not found.")
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
    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());
            return ApiService::response(new RoleResource($role), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Delete a role",
     *     description="Remove a role from the system",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role not found.")
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
            $this->roleService->deleteRole($id);
            return ApiService::response(['message' => __('messages.role_deleted')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }
}
