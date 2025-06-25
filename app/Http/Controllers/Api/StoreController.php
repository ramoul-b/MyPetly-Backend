<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Services\StoreService;
use App\Services\ApiService;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Stores", description="Gestion des boutiques")
 */
class StoreController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * @OA\Get(
     *     path="/stores",
     *     tags={"Stores"},
     *     summary="Liste des boutiques",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste récupérée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new Store());
            $stores = $this->storeService->getAll();
            return ApiService::response(StoreResource::collection($stores), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/stores",
     *     tags={"Stores"},
     *     summary="Créer une boutique",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="provider_id", type="integer", example=1),
     *         @OA\Property(property="name", type="object", example={"fr":"Ma boutique"}),
     *         @OA\Property(property="description", type="object", example={"fr":"Description"})
     *     )),
     *     @OA\Response(response=201, description="Boutique créée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Store());
            $store = $this->storeService->create($request->validated());
            return ApiService::response(new StoreResource($store), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/stores/{id}",
     *     tags={"Stores"},
     *     summary="Afficher une boutique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Boutique trouvée"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $store = $this->storeService->find($id);
            $this->authorize('view', $store);
            return ApiService::response(new StoreResource($store), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Store not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/stores/{id}",
     *     tags={"Stores"},
     *     summary="Mettre à jour une boutique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Boutique mise à jour"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(UpdateStoreRequest $request, int $id): JsonResponse
    {
        try {
            $store = $this->storeService->find($id);
            $this->authorize('update', $store);
            $updated = $this->storeService->update($store, $request->validated());
            return ApiService::response(new StoreResource($updated), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Store not found', 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/stores/{id}",
     *     tags={"Stores"},
     *     summary="Supprimer une boutique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Supprimé"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $store = $this->storeService->find($id);
            $this->authorize('delete', $store);
            $this->storeService->delete($store);
            return ApiService::response(['message' => 'Store deleted'], 200);
        } catch (\Throwable $e) {
            return ApiService::response('Store not found', 404);
        }
    }
}
