<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreSettingRequest;
use App\Http\Requests\UpdateStoreSettingRequest;
use App\Http\Resources\StoreSettingResource;
use App\Models\StoreSetting;
use App\Services\ApiService;
use App\Services\StoreSettingService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Store Settings", description="Configuration des magasins")
 */
class StoreSettingController extends Controller
{
    public function __construct(private readonly StoreSettingService $storeSettingService)
    {
    }

    /**
     * @OA\Get(
     *     path="/store-settings",
     *     tags={"Store Settings"},
     *     summary="Liste des configurations",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des paramètres"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new StoreSetting());

            $settings = $this->storeSettingService->list();

            return ApiService::response(StoreSettingResource::collection($settings), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/store-settings",
     *     tags={"Store Settings"},
     *     summary="Créer une configuration",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Paramètres créés"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(StoreStoreSettingRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new StoreSetting());

            $setting = $this->storeSettingService->create($request->validated());

            return ApiService::response(new StoreSettingResource($setting), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/store-settings/{setting}",
     *     tags={"Store Settings"},
     *     summary="Afficher une configuration",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="setting", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Paramètres"),
     *     @OA\Response(response=404, description="Introuvable")
     * )
     */
    public function show(StoreSetting $setting): JsonResponse
    {
        try {
            $this->authorize('view', $setting);

            return ApiService::response(new StoreSettingResource($setting->load('store')), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Store setting not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/store-settings/{setting}",
     *     tags={"Store Settings"},
     *     summary="Mettre à jour une configuration",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="setting", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Paramètres mis à jour"),
     *     @OA\Response(response=404, description="Introuvable")
     * )
     */
    public function update(UpdateStoreSettingRequest $request, StoreSetting $setting): JsonResponse
    {
        try {
            $this->authorize('update', $setting);

            $updated = $this->storeSettingService->update($setting, $request->validated());

            return ApiService::response(new StoreSettingResource($updated), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Store setting not found', 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/store-settings/{setting}",
     *     tags={"Store Settings"},
     *     summary="Supprimer une configuration",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="setting", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Paramètres supprimés"),
     *     @OA\Response(response=404, description="Introuvable")
     * )
     */
    public function destroy(StoreSetting $setting): JsonResponse
    {
        try {
            $this->authorize('delete', $setting);

            $this->storeSettingService->delete($setting);

            return ApiService::response(['message' => 'Store setting deleted'], 200);
        } catch (\Throwable $e) {
            return ApiService::response('Store setting not found', 404);
        }
    }
}
