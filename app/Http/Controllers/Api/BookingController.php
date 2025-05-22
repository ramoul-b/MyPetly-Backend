<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\ApiService;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Services\BookingService;

class BookingController extends Controller
{
    

public function __construct(private BookingService $bookingService) {}

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
     *     summary="Créé une nouvelle réservation payée",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"service_id","provider_id","appointment_date","time","payment_intent"},
     *             @OA\Property(property="service_id",      type="integer", example=1),
     *             @OA\Property(property="provider_id",     type="integer", example=3),
     *             @OA\Property(property="appointment_date",type="string",  format="date", example="2025-05-22"),
     *             @OA\Property(property="time",            type="string",  example="09:30"),
     *             @OA\Property(property="payment_intent",  type="string",  example="pi_3RO…"),
     *             @OA\Property(property="currency",        type="string",  example="eur"),
     *             @OA\Property(property="notes",           type="string",  example="Mon chien est sensible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Réservation créée",
     *         @OA\JsonContent(
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="provider_id", type="integer", example=3),
     *             @OA\Property(property="appointment_date", type="string", example="2025-05-22"),
     *             @OA\Property(property="sex", type="string", enum={"male", "female"}, example="male"),
     *             @OA\Property(property="time", type="string", example="09:30"),
     *             @OA\Property(property="payment_intent", type="string", example="i_3RO…"),
     *             @OA\Property(property="currency", type="string", example="eur"),
     *             @OA\Property(property="notes", type="string", example="Mon chien est sensible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Créneau déjà réservé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Créneau déjà réservé"),
     *             @OA\Property(property="errors", type="object", example={"name": {"Créneau déjà réservé"}})     
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Paiement non confirmé ou validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Paiement non confirmé ou validation"),
     *             @OA\Property(property="errors", type="object", example={"name": {"Paiement non confirmé ou validation"}})     
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur serveur"),
     *             @OA\Property(property="errors", type="object", example={"name": {"Erreur serveur"}})     
     *         )
     *     ),
     * )
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            // Stripe : vérification paiement
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($request->payment_intent);
            if ($intent->status !== 'succeeded') {
                return ApiService::response(['message'=>'Paiement non confirmé'], 422);
            }

            // Appel au service
            $booking = $this->bookingService->createConfirmed($request->validated());

            return ApiService::response(new BookingResource($booking), 201);

        } catch (\Exception $e) {
            return ApiService::response([
                'message' => 'Erreur lors de la réservation',
                'error'   => $e->getMessage()
            ], 500);
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

/**
     * @OA\Get(
     *      path="/bookings/mine",
     *      operationId="myBookings",
     *      tags={"Bookings"},
     *      summary="Liste des réservations de l’utilisateur connecté",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Opération réussie"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer"),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(ref="#/components/schemas/BookingResource")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function myBookings(): JsonResponse
    {
        try {
            // même logique que index, mais filtrée
            $bookings = Booking::with(['service', 'provider'])
                ->where('user_id', Auth::id())
                ->get();

            // on renvoie la collection directement, comme index
            return ApiService::response(
                BookingResource::collection($bookings),
                200
            );
        } catch (\Exception $e) {
            return ApiService::response(
                ['message' => 'Erreur lors de la récupération des réservations.', 'error' => $e->getMessage()],
                500
            );
        }
    }
}
