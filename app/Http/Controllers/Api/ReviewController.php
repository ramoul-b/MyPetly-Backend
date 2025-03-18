<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ApiService;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/reviews",
     *     tags={"Reviews"},
     *     summary="Liste tous les avis",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des avis récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $reviews = Review::with(['service', 'user'])->get();
            return ApiService::response(ReviewResource::collection($reviews), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération des avis.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/reviews",
     *     tags={"Reviews"},
     *     summary="Crée un nouvel avis",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="service_id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=1),
     *         @OA\Property(property="rating", type="integer", example=5),
     *         @OA\Property(property="comment", type="string", example="Excellent service !")
     *     )),
     *     @OA\Response(response=201, description="Avis créé avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        try {
            $review = Review::create($request->validated());
            return ApiService::response(new ReviewResource($review), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la création de l\'avis.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Affiche un avis spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Avis récupéré avec succès"),
     *     @OA\Response(response=404, description="Avis introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function show(Review $review): JsonResponse
    {
        try {
            return ApiService::response(new ReviewResource($review->load(['service', 'user'])), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération de l\'avis.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Met à jour un avis existant",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="rating", type="integer", example=4),
     *         @OA\Property(property="comment", type="string", example="Très bon, mais peut mieux faire")
     *     )),
     *     @OA\Response(response=200, description="Avis mis à jour avec succès"),
     *     @OA\Response(response=404, description="Avis introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        try {
            $review->update($request->validated());
            return ApiService::response(new ReviewResource($review), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la mise à jour de l\'avis.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Supprime un avis",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Avis supprimé avec succès"),
     *     @OA\Response(response=404, description="Avis introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function destroy(Review $review): JsonResponse
    {
        try {
            $review->delete();
            return ApiService::response(['message' => 'Avis supprimé avec succès.'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la suppression de l\'avis.', 'error' => $e->getMessage()], 500);
        }
    }
}
