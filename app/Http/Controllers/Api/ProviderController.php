<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderRequest;
use App\Http\Requests\UpdateProviderRequest;
use App\Http\Resources\ProviderResource;
use App\Services\ApiService;
use App\Models\Provider;
use Illuminate\Http\JsonResponse;

class ProviderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/providers",
     *     tags={"Providers"},
     *     summary="Liste tous les prestataires",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des prestataires récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $providers = Provider::all();
            return ApiService::response(ProviderResource::collection($providers), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération des prestataires.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/providers",
     *     tags={"Providers"},
     *     summary="Crée un nouveau prestataire",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="Dr. John Doe"),
     *         @OA\Property(property="email", type="string", example="john@example.com"),
     *         @OA\Property(property="phone", type="string", example="+123456789"),
     *         @OA\Property(property="address", type="string", example="123 Street, City"),
     *         @OA\Property(property="specialization", type="string", example="Vet"),
     *         @OA\Property(property="rating", type="number", example=4.8)
     *     )),
     *     @OA\Response(response=201, description="Prestataire créé avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function store(StoreProviderRequest $request): JsonResponse
    {
        try {
            $provider = Provider::create($request->validated());
            return ApiService::response(new ProviderResource($provider), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la création du prestataire.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/providers/{id}",
     *     tags={"Providers"},
     *     summary="Affiche un prestataire spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Prestataire récupéré avec succès"),
     *     @OA\Response(response=404, description="Prestataire introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function show(Provider $provider): JsonResponse
    {
        try {
            return ApiService::response(new ProviderResource($provider), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération du prestataire.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/providers/{id}",
     *     tags={"Providers"},
     *     summary="Met à jour un prestataire existant",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="Dr. John Doe Updated"),
     *         @OA\Property(property="email", type="string", example="johnupdated@example.com"),
     *         @OA\Property(property="phone", type="string", example="+987654321"),
     *         @OA\Property(property="address", type="string", example="456 Avenue, City"),
     *         @OA\Property(property="specialization", type="string", example="Grooming"),
     *         @OA\Property(property="rating", type="number", example=4.9)
     *     )),
     *     @OA\Response(response=200, description="Prestataire mis à jour avec succès"),
     *     @OA\Response(response=404, description="Prestataire introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function update(UpdateProviderRequest $request, Provider $provider): JsonResponse
    {
        try {
            $provider->update($request->validated());
            return ApiService::response(new ProviderResource($provider), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la mise à jour du prestataire.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/providers/{id}",
     *     tags={"Providers"},
     *     summary="Supprime un prestataire",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Prestataire supprimé avec succès"),
     *     @OA\Response(response=404, description="Prestataire introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function destroy(Provider $provider): JsonResponse
    {
        try {
            $provider->delete();
            return ApiService::response(['message' => 'Prestataire supprimé avec succès.'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la suppression du prestataire.', 'error' => $e->getMessage()], 500);
        }
    }
}
