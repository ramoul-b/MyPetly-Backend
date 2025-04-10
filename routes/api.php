<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\CollarController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;


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
        Route::get('/animals', [AnimalController::class, 'index']);
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

        //  Services
        Route::get('/services', [ServiceController::class, 'index']); // Liste des services
        Route::post('/services', [ServiceController::class, 'store']); // Création d'un service
        Route::get('/services/{id}', [ServiceController::class, 'show']); // Détails d'un service
        Route::put('/services/{id}', [ServiceController::class, 'update']); // Mise à jour d'un service
        Route::delete('/services/{id}', [ServiceController::class, 'destroy']); // Suppression d'un service
        
        //  Providers
        Route::get('/providers', [ProviderController::class, 'index']); // Liste des prestataires
        Route::post('/providers', [ProviderController::class, 'store']); // Création d'un prestataire
        Route::get('/providers/{provider}', [ProviderController::class, 'show']); // Détails d'un prestataire
        Route::put('/providers/{provider}', [ProviderController::class, 'update']); // Mise à jour d'un prestataire
        Route::delete('/providers/{provider}', [ProviderController::class, 'destroy']); // Suppression d'un prestataire

        //  Categories
        Route::get('/categories', [CategoryController::class, 'index']); // Liste des catégories
        Route::post('/categories', [CategoryController::class, 'store']); // Création d'une catégorie
        Route::get('/categories/{category}', [CategoryController::class, 'show']); // Détails d'une catégorie
        Route::put('/categories/{category}', [CategoryController::class, 'update']); // Mise à jour d'une catégorie
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']); // Suppression d'une catégorie

        //  Bookings
        Route::get('/bookings', [BookingController::class, 'index']); // Liste des réservations
        Route::post('/bookings', [BookingController::class, 'store']); // Création d'une réservation
        Route::get('/bookings/{booking}', [BookingController::class, 'show']); // Détails d'une réservation
        Route::put('/bookings/{booking}', [BookingController::class, 'update']); // Mise à jour d'une réservation
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']); // Suppression d'une réservation

        //  Reviews
        Route::get('/reviews', [ReviewController::class, 'index']); // Liste des avis
        Route::post('/reviews', [ReviewController::class, 'store']); // Création d'un avis
        Route::get('/reviews/{review}', [ReviewController::class, 'show']); // Détails d'un avis
        Route::put('/reviews/{review}', [ReviewController::class, 'update']); // Mise à jour d'un avis
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']); // Suppression d'un avis
    });
});
