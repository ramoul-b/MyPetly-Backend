<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\ApiService;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/bookings",
     *     tags={"Bookings"},
     *     summary="Liste toutes les réservations",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des réservations récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $bookings = Booking::with(['service', 'user'])->get();
            return ApiService::response(BookingResource::collection($bookings), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération des réservations.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/bookings",
     *     tags={"Bookings"},
     *     summary="Crée une nouvelle réservation",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="service_id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=1),
     *         @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-04-20 10:00:00"),
     *         @OA\Property(property="status", type="string", example="pending"),
     *         @OA\Property(property="notes", type="string", example="Préférences particulières")
     *     )),
     *     @OA\Response(response=201, description="Réservation créée avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = Booking::create($request->validated());
            return ApiService::response(new BookingResource($booking), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la création de la réservation.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/bookings/{id}",
     *     tags={"Bookings"},
     *     summary="Affiche une réservation spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Réservation récupérée avec succès"),
     *     @OA\Response(response=404, description="Réservation introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function show(Booking $booking): JsonResponse
    {
        try {
            return ApiService::response(new BookingResource($booking->load(['service', 'user'])), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération de la réservation.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/bookings/{id}",
     *     tags={"Bookings"},
     *     summary="Met à jour une réservation existante",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-04-21 11:00:00"),
     *         @OA\Property(property="status", type="string", example="confirmed"),
     *         @OA\Property(property="notes", type="string", example="Modifier les préférences")
     *     )),
     *     @OA\Response(response=200, description="Réservation mise à jour avec succès"),
     *     @OA\Response(response=404, description="Réservation introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        try {
            $booking->update($request->validated());
            return ApiService::response(new BookingResource($booking), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la mise à jour de la réservation.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/bookings/{id}",
     *     tags={"Bookings"},
     *     summary="Supprime une réservation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Réservation supprimée avec succès"),
     *     @OA\Response(response=404, description="Réservation introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function destroy(Booking $booking): JsonResponse
    {
        try {
            $booking->delete();
            return ApiService::response(['message' => 'Réservation supprimée avec succès.'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la suppression de la réservation.', 'error' => $e->getMessage()], 500);
        }
    }
}
