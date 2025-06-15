<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderRequest;
use App\Http\Requests\UpdateProviderRequest;
use App\Http\Resources\ProviderResource;
use App\Services\ProviderService;
use App\Models\Provider;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Providers", description="Gestion des prestataires de services")
 */
class ProviderController extends Controller
{
    protected $providerService;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    /**
     * @OA\Get(
     *     path="/providers",
     *     tags={"Providers"},
     *     summary="Liste tous les prestataires",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des prestataires récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new Provider());
            $providers = $this->providerService->getAll();
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
     *         @OA\Property(property="name", type="object",
     *             example={"fr": "Toiletteur", "en": "Groomer", "es": "Peluquero"}
     *         ),
     *         @OA\Property(property="email", type="string", example="john@example.com"),
     *         @OA\Property(property="phone", type="string", example="+123456789"),
     *         @OA\Property(property="address", type="string", example="123 Street, City"),
     *         @OA\Property(property="specialization", type="object",
     *             example={"fr": "Vétérinaire", "en": "Veterinarian", "es": "Veterinario"}
     *         ),
     *         @OA\Property(property="rating", type="number", example=4.8)
     *     )),
     *     @OA\Response(response=201, description="Prestataire créé avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function store(StoreProviderRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Provider());
            $provider = $this->providerService->create($request->validated());
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
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $provider = Provider::with('services')->findOrFail($id);
            $this->authorize('view', $provider);
            return ApiService::response(new ProviderResource($provider));
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Provider introuvable'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/providers/by-user/{userId}",
     *     tags={"Providers"},
     *     summary="Récupérer le prestataire associé à un utilisateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Prestataire récupéré avec succès"),
     *     @OA\Response(response=404, description="Prestataire introuvable"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function getByUserId(int $userId): JsonResponse
    {
        try {
            $provider = $this->providerService->findByUserId($userId);
            $this->authorize('view', $provider);
            return ApiService::response(new ProviderResource($provider));
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Provider introuvable'], 404);
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
     *         @OA\Property(property="name", type="object",
     *             example={"fr": "Toiletteur", "en": "Groomer", "es": "Peluquero"}
     *         ),
     *         @OA\Property(property="email", type="string", example="johnupdated@example.com"),
     *         @OA\Property(property="phone", type="string", example="+987654321"),
     *         @OA\Property(property="address", type="string", example="456 Avenue, City"),
     *         @OA\Property(property="specialization", type="object",
     *             example={"fr": "Grooming", "en": "Grooming", "es": "Peluquería"}
     *         ),
     *         @OA\Property(property="rating", type="number", example=4.9)
     *     )),
     *     @OA\Response(response=200, description="Prestataire mis à jour avec succès"),
     *     @OA\Response(response=404, description="Prestataire introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function update(UpdateProviderRequest $request, $id): JsonResponse
    {
        try {
            $provider = $this->providerService->find($id);
            $this->authorize('update', $provider);
            $updatedProvider = $this->providerService->update($provider, $request->validated());
            return ApiService::response(new ProviderResource($updatedProvider), 200);
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
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $provider = $this->providerService->find($id);
            $this->authorize('delete', $provider);
            $this->providerService->delete($id);
            return ApiService::response(['message' => 'Prestataire supprimé avec succès.'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la suppression du prestataire.', 'error' => $e->getMessage()], 500);
        }
    }
}
