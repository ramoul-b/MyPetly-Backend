<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

Route::prefix('v1')->group(function () {
    // Routes publiques
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);

    // Routes protégées par le middleware “auth”
    Route::middleware(['auth:sanctum', 'locale'])->group(function () {

        // AuthController
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::patch('/update-password', [AuthController::class, 'updatePassword']);
        Route::post('/deactivate-account', [AuthController::class, 'deactivateAccount']);
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

        // AccountController
        Route::get('/account/profile', [AccountController::class, 'getProfile']);
        Route::post('/account/profile', [AccountController::class, 'updateProfile']);
        Route::put('/account/change-password', [AccountController::class, 'changePassword']);
        Route::post('/deactivate-account', [AccountController::class, 'deactivateAccount']);
        Route::delete('/delete-account', [AccountController::class, 'deleteAccount']);

        // AnimalController
        Route::post('/animals/{id}/collar', [AnimalController::class, 'attachCollar']);
        Route::put('/animals/{id}/lost', [AnimalController::class, 'markAsLost']);
        Route::put('/animals/{id}/found', [AnimalController::class, 'markAsFound']);
        Route::get('/scan/{collarId}', [AnimalController::class, 'scanCollar']);

        // // CollarController
        Route::get('collars', [CollarController::class, 'index']);
        Route::post('collars', [CollarController::class, 'store']);
        Route::get('collars/{id}', [CollarController::class, 'show']);
        Route::put('collars/{id}', [CollarController::class, 'update']);
        Route::delete('collars/{id}', [CollarController::class, 'destroy']);
        Route::post('collars/{collarId}/assign/{animalId}', [CollarController::class, 'assignToAnimal']);


    });
});
