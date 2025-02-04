<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Services\ApiService;

/**
 * @OA\Info(
 *     title="MyPetly API Documentation",
 *     version="1.0.0",
 *     description="This is the API documentation for MyPetly.",
 *     @OA\Contact(
 *         email="support@mypetly.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Local development server"
 * )
* @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{

/**
 * @OA\Post(
 *     path="/register",
 *     tags={"Authentication"},
 *     summary="Register a new user",
 *     description="Create a new user account and return an access token.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "password_confirmation"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="generated-access-token"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *                 @OA\Property(property="email_verified_at", type="string", format="datetime", example="null"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2025-01-20T20:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2025-01-20T20:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The provided data is invalid."),
 *             @OA\Property(property="errors", type="object", example={
 *                 "email": {"The email field is required."},
 *                 "password": {"The password confirmation does not match."}
 *             })
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
 *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
 *         )
 *     )
 * )
 */

    public function register(StoreUserRequest $request)
    {
        try {
            // Valider les données via StoreUserRequest (déjà validées automatiquement)
            $validated = $request->validated();

            // Créer l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Générer un token pour l'utilisateur
            $token = $user->createToken('mypetly')->plainTextToken;

            // Retourner une réponse avec UserResource
            return ApiService::response([
                'access_token' => $token,
                'user' => new UserResource($user),
            ], 201);

        } catch (\Exception $e) {
            // Gérer les exceptions imprévues
            return ApiService::response([
                'message' => __('messages.operation_failed'),
                'error' => $e->getMessage(), // Facultatif : Inclure l'erreur détaillée en mode debug
            ], 500);
        }
    }

    /**
 * @OA\Post(
 *     path="/login",
 *     tags={"Authentication"},
 *     summary="Login a user",
 *     description="Authenticate a user and return an access token.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User authenticated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="generated-access-token"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *                 @OA\Property(property="email_verified_at", type="string", format="datetime", example="null"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2025-01-20T20:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2025-01-20T20:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation error"),
 *             @OA\Property(property="errors", type="object", example={
 *                 "email": {"The email field is required."},
 *                 "password": {"The password field is required."}
 *             })
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid credentials")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
 *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
 *         )
 *     )
 * )
 */
    public function login(Request $request)
{
    try {
        // Valider les données de la requête
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiService::response($validator->errors(), 422);
        }

        // Vérifier les identifiants
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiService::response([
                'message' => __('messages.invalid_credentials'),
            ], 401);
        }

        // Générer un token
        $token = $user->createToken('mypetly')->plainTextToken;

        return ApiService::response([
            'access_token' => $token,
            'user'         => new UserResource($user),
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Détail facultatif pour le mode debug
        ], 500);
    }
}



    /**
 * @OA\Post(
 *     path="/logout",
 *     tags={"Authentication"},
 *     summary="Logout a user",
 *     description="Revoke the current access token.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User logged out successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logged out")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
 *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
 *         )
 *     )
 * )
 */
    public function logout(Request $request)
{
    try {
        // Révoquer le token d'accès actuel
        $request->user()->currentAccessToken()->delete();

        return ApiService::response([
            'message' => __('messages.logged_out'),
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Détail facultatif pour le mode debug
        ], 500);
    }
}


/**
 * @OA\Get(
 *     path="/user-profile",
 *     tags={"Authentication"},
 *     summary="Get authenticated user profile",
 *     description="Retrieve the profile of the currently authenticated user.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User profile retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *                 @OA\Property(property="email_verified_at", type="string", format="datetime", example="null"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2025-01-20T20:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2025-01-20T20:00:00Z")
 *             )
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
public function userProfile(Request $request)
{
    try {
        // Vérifier si l'utilisateur est authentifié
        $user = $request->user();
        if (!$user) {
            return ApiService::response([
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        // Retourner les informations de l'utilisateur via une ressource
        return ApiService::response([
            'user' => new UserResource($user),
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Optionnel en mode debug
        ], 500);
    }
}


/**
 * @OA\Post(
 *     path="/refresh-token",
 *     tags={"Authentication"},
 *     summary="Refresh access token",
 *     description="Generate a new access token for the authenticated user.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="New access token generated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="new-generated-token"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *                 @OA\Property(property="email_verified_at", type="string", format="datetime", example="null"),
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
public function refreshToken(Request $request)
{
    try {
        // Vérifier qu'un utilisateur authentifié est présent
        $user = $request->user();
        if (!$user) {
            return ApiService::response([
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        // Générer un nouveau token
        $newToken = $user->createToken('mypetly')->plainTextToken;

        return ApiService::response([
            'access_token' => $newToken,
            'user' => new UserResource($user), // Retourner l'utilisateur formaté si nécessaire
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Facultatif en mode debug
        ], 500);
    }
}


/**
 * @OA\Post(
 *     path="/forgot-password",
 *     tags={"Authentication"},
 *     summary="Send password reset link",
 *     description="Send an email with a password reset link.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset link sent successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password reset link sent successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation error"),
 *             @OA\Property(property="errors", type="object", example={
 *                 "email": {"The email field is required."}
 *             })
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
 *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
 *         )
 *     )
 * )
 */
public function forgotPassword(Request $request)
{
    try {
        // Validation des données d'entrée
        $request->validate(['email' => 'required|email']);

        // Envoi du lien de réinitialisation
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return ApiService::response([
                'message' => __('messages.password_reset_link_sent'),
            ], 200);
        }

        return ApiService::response([
            'message' => __('messages.password_reset_failed'),
        ], 400);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Facultatif : détails en mode debug
        ], 500);
    }
}

/**
 * @OA\Post(
 *     path="/reset-password",
 *     tags={"Authentication"},
 *     summary="Reset password",
 *     description="Reset the user password using a valid token.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"token", "email", "password", "password_confirmation"},
 *             @OA\Property(property="token", type="string", example="reset-token"),
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password reset successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Password reset failed.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The provided token is invalid or expired.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation error"),
 *             @OA\Property(property="errors", type="object", example={
 *                 "email": {"The email field is required."},
 *                 "password": {"The password confirmation does not match."}
 *             })
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred while processing your request."),
 *             @OA\Property(property="error", type="string", example="Detailed error message if in debug mode")
 *         )
 *     )
 * )
 */
public function resetPassword(Request $request)
{
    try {
        // Validation des données d'entrée
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Réinitialisation du mot de passe
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiService::response([
                'message' => __('messages.password_reset_successful'),
            ], 200);
        }

        return ApiService::response([
            'message' => __('messages.password_reset_failed'),
        ], 400);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Facultatif : détails en mode debug
        ], 500);
    }
}

/**
 * @OA\Post(
 *     path="/update-password",
 *     tags={"Authentication"},
 *     summary="Update user password",
 *     description="Allows an authenticated user to update their password.",
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
 *         description="Password updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password updated successfully.")
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
 *                 "new_password": {"The new password field is required."}
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
public function updatePassword(Request $request)
{
    try {
        // Validation des données
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return ApiService::response([
                'message' => __('messages.current_password_incorrect'),
            ], 400);
        }

        // Mettre à jour le mot de passe
        $request->user()->update(['password' => Hash::make($request->new_password)]);

        return ApiService::response([
            'message' => __('messages.password_updated'),
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Facultatif : détaillé en mode debug
        ], 500);
    }
}

/**
 * @OA\Post(
 *     path="/verify-email",
 *     tags={"Authentication"},
 *     summary="Verify email address",
 *     description="Verify the user's email address using a token.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"token"},
 *             @OA\Property(property="token", type="string", example="verification-token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email verified successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Email verified successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or expired verification token.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The verification token is invalid or expired.")
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
public function verifyEmail(Request $request)
{
    try {
        // Validation des données d'entrée
        $request->validate(['token' => 'required|string']);

        // Trouver l'utilisateur via le token
        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return ApiService::response([
                'message' => __('messages.invalid_verification_token'),
            ], 400);
        }

        // Vérifier si l'utilisateur est déjà vérifié
        if ($user->hasVerifiedEmail()) {
            return ApiService::response([
                'message' => __('messages.email_already_verified'),
            ], 200);
        }

        // Marquer l'email comme vérifié
        $user->markEmailAsVerified();

        return ApiService::response([
            'message' => __('messages.email_verified_successfully'),
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Optionnel : détails en mode debug
        ], 500);
    }
}

/**
 * @OA\Post(
 *     path="/resend-verification-email",
 *     tags={"Authentication"},
 *     summary="Resend verification email",
 *     description="Resend the email verification link to the user.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verification email resent successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Verification email resent successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found.")
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
public function resendVerificationEmail(Request $request)
{
    try {
        // Validation des données d'entrée
        $request->validate(['email' => 'required|email']);

        // Trouver l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiService::response([
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        // Vérifier si l'utilisateur est déjà vérifié
        if ($user->hasVerifiedEmail()) {
            return ApiService::response([
                'message' => __('messages.email_already_verified'),
            ], 200);
        }

        // Envoyer un nouvel email de vérification
        $user->sendEmailVerificationNotification();

        return ApiService::response([
            'message' => __('messages.verification_email_resent'),
        ], 200);

    } catch (\Exception $e) {
        return ApiService::response([
            'message' => __('messages.operation_failed'),
            'error' => $e->getMessage(), // Optionnel : détails en mode debug
        ], 500);
    }
}





}

