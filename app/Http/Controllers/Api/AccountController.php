<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use App\Services\ApiService;
use App\Services\UserService;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class AccountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/account/profile",
     *     tags={"Account"},
     *     summary="Get account profile",
     *     description="Retrieve the profile of the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123456789"),
     *                 @OA\Property(property="address", type="string", example="123 Street, City"),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2025-01-20T20:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2025-01-20T20:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
     *         )
     *     )
     * )
     */
    public function getProfile(Request $request)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = $request->user();

            // Vérifier si un utilisateur est authentifié
            if (!$user) {
                return ApiService::response([
                    'message' => __('messages.unauthenticated'),
                ], 401);
            }

            // Retourner les données du profil via une ressource
            return ApiService::response(new AccountResource($user), 200);

        } catch (\Exception $e) {
            // Gérer les exceptions
            return ApiService::response([
                'message' => __('messages.operation_failed'),
                'error' => $e->getMessage(), // Optionnel : affiché en mode debug
            ], 500);
        }
    }


/**
 * @OA\Post(
 *     path="/account/profile",
 *     tags={"Account"},
 *     summary="Update account profile",
 *     description="Update the profile information of the authenticated user, including profile photo.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name", "email"},
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *                 @OA\Property(property="phone", type="string", example="123456789"),
 *                 @OA\Property(property="address", type="string", example="123 Street, City"),
 *                 @OA\Property(property="photo", type="string", format="binary", description="User profile photo")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Profile updated successfully."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation error."),
 *             @OA\Property(property="errors", type="object", example={
 *                 "name": {"The name field is required."},
 *                 "email": {"The email must be a valid email address."},
 *                 "photo": {"The photo must be an image."}
 *             })
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
 *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
 *         )
 *     )
 * )
 */

    public function updateProfile(UpdateUserRequest $request, UserService $userService)
    {
    try {
        // Récupérer l'utilisateur authentifié
        $user = $request->user();
        $this->authorize('update', $user);

        // Mise à jour via le service
        $updatedUser = $userService->updateUser($user->id, $request->validated());

        return ApiService::response([
            'message' => __('messages.profile_updated'),
            'data' => new UserResource($updatedUser),
        ], 200);
    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * @OA\Put(
     *     path="/account/change-password",
     *     tags={"Account"},
     *     summary="Change account password",
     *     description="Allows the authenticated user to change their password.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password changed successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Current password is incorrect.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The current password is incorrect.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "new_password": {"The new password must be at least 8 characters."}
     *             })
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
     *         )
     *     )
     * )
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user();
            $this->authorize('update', $user);

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return ApiService::response([
                    'message' => __('messages.current_password_incorrect'),
                ], 400);
            }

            // Mettre à jour le mot de passe
            $user->update(['password' => Hash::make($request->new_password)]);

            return ApiService::response([
                'message' => __('messages.password_changed'),
            ], 200);

        } catch (\Exception $e) {
            return ApiService::response([
                'message' => __('messages.operation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/deactivate-account",
     *     tags={"Account"},
     *     summary="Deactivate user account",
     *     description="Deactivate the authenticated user's account by marking it as inactive.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Account deactivated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Account deactivated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
     *         )
     *     )
     * )
     */
    public function deactivateAccount(Request $request)
    {
        try {
            $user = $request->user();
            $this->authorize('delete', $user);
            $user->update(['status' => 'inactive']);

            return ApiService::response([
                'message' => __('messages.account_deactivated'),
            ], 200);
        } catch (\Exception $e) {
            return ApiService::response([
                'message' => __('messages.operation_failed'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/delete-account",
     *     tags={"Account"},
     *     summary="Delete user account",
     *     description="Permanently delete the authenticated user's account.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Account deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Account deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
     *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
     *         )
     *     )
     * )
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();
            $this->authorize('delete', $user);
            $user->delete();

            return ApiService::response([
                'message' => __('messages.account_deleted'),
            ], 200);
        } catch (\Exception $e) {
            return ApiService::response([
                'message' => __('messages.operation_failed'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    

}
