<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Services\ApiService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="List all users",
     *     description="Retrieve a list of all users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="string", example="Admin")),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:25:55Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', User::class);
            $users = $this->userService->getAllUsers();
            return ApiService::response(UserResource::collection($users), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Add a new user to the system",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="Admin")),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:25:55Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
     *         )
     *     )
     * )
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->authorize('create', User::class);
            $user = $this->userService->createUser($request->validated());
            return ApiService::response(new UserResource($user), 201);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Get user details",
     *     description="Retrieve details of a specific user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="Admin")),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:25:55Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('view', $user);
            if (!$user) {
                return ApiService::response(['message' => __('messages.user_not_found')], 404);
            }
            return ApiService::response(new UserResource($user), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Update user details",
     *     description="Update details of an existing user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", example="janedoe@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="integer", example=2))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", example="janedoe@example.com"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="Editor")),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:30:55Z")
     *         )
     *     ), 
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('update', $user);
            $user = $this->userService->updateUser($id, $request->validated());
            if (!$user) {
                return ApiService::response(['message' => __('messages.user_not_found')], 404);
            }
            return ApiService::response(new UserResource($user), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Remove a user from the system",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('delete', $user);
            $deleted = $this->userService->deleteUser($id);
            if (!$deleted) {
                return ApiService::response(['message' => __('messages.user_not_found')], 404);
            }
            return ApiService::response(['message' => __('messages.user_deleted')], 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/users/search",
     *     tags={"Users"},
     *     summary="Search for users",
     *     description="Search for users by name, email, or other criteria",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query string",
     *         @OA\Schema(type="string", example="jane")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jane Doe"),
     *                 @OA\Property(property="email", type="string", example="janedoe@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T17:25:55Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T17:30:55Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object", example={"query": {"The query field is required."}})
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
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:255',
        ]);

        try {
            $query = $validated['query'];
            $users = $this->userService->searchUsers($query);

            return ApiService::response(UserResource::collection($users), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/users/{id}/assign-role",
     *     tags={"Users"},
     *     summary="Assign a role to a user",
     *     description="Assign a specific role to a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role_id"},
     *             @OA\Property(property="role_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role assigned successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User or role not found.")
     *         )
     *     )
     * )
     */
    public function assignRole(Request $request, $id)
    {
        $validated = $request->validate(['role_id' => 'required|exists:roles,id']);
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('update', $user);
            $result = $this->userService->assignRole($id, $validated['role_id']);
            if (!$result) {
                return ApiService::response(['message' => __('messages.user_or_role_not_found')], 404);
            }
            return ApiService::response(['message' => __('messages.role_assigned')], 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }


        /**
     * @OA\Post(
     *     path="/users/{id}/revoke-role",
     *     tags={"Users"},
     *     summary="Revoke a role from a user",
     *     description="Remove a specific role from a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role_id"},
     *             @OA\Property(property="role_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role revoked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role revoked successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User or role not found.")
     *         )
     *     )
     * )
     */
    public function revokeRole(Request $request, $id)
    {
        $validated = $request->validate(['role_id' => 'required|exists:roles,id']);
        try {
            $result = $this->userService->revokeRole($id, $validated['role_id']);
            if (!$result) {
                return ApiService::response(['message' => __('messages.user_or_role_not_found')], 404);
            }
            return ApiService::response(['message' => __('messages.role_revoked')], 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}/roles",
     *     tags={"Users"},
     *     summary="Get user roles",
     *     description="Retrieve roles assigned to a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, example=1),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function getRoles($id)
    {
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('view', $user);
            return ApiService::response($this->userService->getRoles($id));
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/users/{id}/roles",
     *     tags={"Users"},
     *     summary="Assign roles to user",
     *     description="Assign one or more roles to a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, example=1),
     *     @OA\RequestBody(@OA\JsonContent(@OA\Property(property="roles", type="array", @OA\Items(type="string")))),
     *     @OA\Response(response=200, description="Roles assigned successfully")
     * )
     */
    public function assignRoles(Request $request, $id)
    {
        $request->validate(['roles' => 'required|array']);
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('update', $user);
            return ApiService::response($this->userService->assignRoles($id, $request->roles));
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}/permissions",
     *     tags={"Users"},
     *     summary="Get user permissions",
     *     description="Retrieve permissions assigned to a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, example=1),
     *     @OA\Response(response=200, description="Permissions list")
     * )
     */
    public function getPermissions($id)
    {
        try {
            $user = $this->userService->findUserById($id);
            $this->authorize('view', $user);
            return ApiService::response($this->userService->getPermissions($id));
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }
}
