<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    AccountController,
    AnimalController,
    CollarController,
    ServiceController,
    CategoryController,
    ProviderController,
    BookingController,
    ReviewController,
    RoleController,
    PermissionController,
    UserController,
    ProviderServiceController,
    StoreController,
    ProductController,
    OrderController,
    OrderItemController,
    CartController,
    PaymentController,
    StripeWebhookController
};


Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Auth Routes - Public
    |--------------------------------------------------------------------------
    */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);

    /*
    |--------------------------------------------------------------------------
    | Protected Routes - Requires auth
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'locale'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Auth & Account Management
        |--------------------------------------------------------------------------
        */
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::patch('/update-password', [AuthController::class, 'updatePassword']);
        Route::post('/deactivate-account', [AuthController::class, 'deactivateAccount']);
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

        Route::get('/account/profile', [AccountController::class, 'getProfile']);
        Route::post('/account/profile', [AccountController::class, 'updateProfile']);
        Route::put('/account/change-password', [AccountController::class, 'changePassword']);

        /*
        |--------------------------------------------------------------------------
        | Animals & Collars
        |--------------------------------------------------------------------------
        */
        Route::get('/animals', [AnimalController::class, 'index']);
        Route::post('/animals', [AnimalController::class, 'store']);
        Route::get('/animals/{id}', [AnimalController::class, 'show']);
        Route::post('/animals/{id}/image', [AnimalController::class, 'uploadImage']);
        Route::put('/animals/{id}', [AnimalController::class, 'update']);
        Route::delete('/animals/{id}', [AnimalController::class, 'destroy']);
        
        Route::post('/animals/{id}/collar', [AnimalController::class, 'attachCollar']);
        Route::put('/animals/{id}/lost', [AnimalController::class, 'markAsLost']);
        Route::put('/animals/{id}/found', [AnimalController::class, 'markAsFound']);
        Route::get('/scan/{collarId}', [AnimalController::class, 'scanCollar']);

        Route::apiResource('collars', CollarController::class);
        Route::post('collars/{collarId}/assign/{animalId}', [CollarController::class, 'assignToAnimal']);

        /*
        |--------------------------------------------------------------------------
        | Services / Providers / Categories
        |--------------------------------------------------------------------------
        */
        Route::get('providers/by-user/{userId}', [ProviderController::class, 'getByUserId']);
        Route::apiResource('providers', ProviderController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('stores', StoreController::class);
        Route::apiResource('products', ProductController::class);

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::get('/{order}/items', [OrderItemController::class, 'index']);
        });
        Route::post('/checkout', [OrderController::class, 'checkout']);

        Route::get('order-items/{id}', [OrderItemController::class, 'show']);

        /*
        |--------------------------------------------------------------------------
        | Bookings / Reviews
        |--------------------------------------------------------------------------
        */
        //Route::apiResource('bookings', BookingController::class);
        Route::apiResource('reviews', ReviewController::class);


                /*
        |--------------------------------------------------------------------------
        | Booking Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('bookings')->group(function () {
             
            Route::get('/', [BookingController::class, 'index']);
            Route::post('/', [BookingController::class, 'store']);
            Route::get('/mine', [BookingController::class, 'myBookings']);
            Route::get('/{id}', [BookingController::class, 'show']);
            Route::put('/{id}', [BookingController::class, 'update']);
            Route::delete('/{id}', [BookingController::class, 'destroy']);
        });
        /*
        |--------------------------------------------------------------------------
        | Users Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('users')->group(function () {
            // Routes CRUD pour les utilisateurs
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
            
            // Routes pour la gestion des rôles et permissions des utilisateurs
            Route::get('/{id}/roles', [UserController::class, 'getRoles']);
            Route::post('/{id}/roles', [UserController::class, 'assignRoles']);
            Route::get('/{id}/permissions', [UserController::class, 'getPermissions']);
            Route::post('/{id}/assign-role', [UserController::class, 'assignRole']);

        });

        /*
        |--------------------------------------------------------------------------
        | Roles 
        |--------------------------------------------------------------------------
        */
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{id}', [RoleController::class, 'show']);
            Route::put('/{id}', [RoleController::class, 'update']);
            Route::delete('/{id}', [RoleController::class, 'destroy']);

            Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions']);
            Route::get('/{id}/permissions', [RoleController::class, 'listPermissions']);
        });

        /*
        |--------------------------------------------------------------------------
        |  Permissions Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{id}', [PermissionController::class, 'show']);
            Route::put('/{id}', [PermissionController::class, 'update']);
            Route::delete('/{id}', [PermissionController::class, 'destroy']);
            Route::get('/{id}/roles', [PermissionController::class, 'getRoles']);
        });


        /*
        |--------------------------------------------------------------------------
        |  Permissions Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('provider-services')->group(function () {
            Route::get('/', [ProviderServiceController::class, 'index']);
            Route::post('/', [ProviderServiceController::class, 'store']);
            Route::get('/{id}', [ProviderServiceController::class, 'show']);
            Route::put('/{id}', [ProviderServiceController::class, 'update']);
            Route::delete('/{id}', [ProviderServiceController::class, 'destroy']);
            Route::get('/by-provider/{provider_id}', [ProviderServiceController::class, 'getByProvider']);
            Route::get('/by-service/{service_id}', [ProviderServiceController::class, 'getByService']);
        });

        Route::prefix('cart')->group(function () {
            Route::post('/add', [CartController::class, 'add']);
            Route::delete('/remove/{item}', [CartController::class, 'remove']);
            Route::put('/update/{item}', [CartController::class, 'update']);
            Route::get('/', [CartController::class, 'index']);
        });
        /*
        |--------------------------------------------------------------------------
        |  Payment
        |--------------------------------------------------------------------------
        */

        Route::prefix('payment-intent')->group(function () {
            Route::post('/', [PaymentController::class, 'createIntent']);
        });


        Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);


    });
});
