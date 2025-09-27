<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryMovementRequest;
use App\Http\Requests\UpdateInventoryMovementRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryMovement;
use App\Services\ApiService;
use App\Services\InventoryMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Inventory", description="Gestion des mouvements de stock")
 */
class InventoryController extends Controller
{
    public function __construct(private readonly InventoryMovementService $inventoryMovementService)
    {
    }

    /**
     * @OA\Get(
     *     path="/inventory-movements",
     *     tags={"Inventory"},
     *     summary="Liste des mouvements de stock",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="store_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="product_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="movement_type", in="query", @OA\Schema(type="string", enum={"in","out"})),
     *     @OA\Response(response=200, description="Liste récupérée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('view', new InventoryMovement());

            $movements = $this->inventoryMovementService->list($request->only(['store_id', 'product_id', 'movement_type', 'per_page']));
            $resource = InventoryMovementResource::collection($movements)->additional([
                'meta' => ['total' => $movements->total()],
            ]);

            return ApiService::response($resource, 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/inventory-movements",
     *     tags={"Inventory"},
     *     summary="Créer un mouvement de stock",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Mouvement créé"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(StoreInventoryMovementRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new InventoryMovement());

            $data = $request->validated();
            $data['user_id'] = $data['user_id'] ?? $request->user()->id;

            $movement = $this->inventoryMovementService->create($data);

            return ApiService::response(new InventoryMovementResource($movement), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/inventory-movements/{movement}",
     *     tags={"Inventory"},
     *     summary="Afficher un mouvement",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="movement", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Mouvement récupéré"),
     *     @OA\Response(response=404, description="Introuvable")
     * )
     */
    public function show(InventoryMovement $movement): JsonResponse
    {
        try {
            $this->authorize('view', $movement);

            return ApiService::response(new InventoryMovementResource($movement->load(['store', 'product', 'user'])), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Inventory movement not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/inventory-movements/{movement}",
     *     tags={"Inventory"},
     *     summary="Mettre à jour un mouvement",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="movement", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Mouvement mis à jour"),
     *     @OA\Response(response=404, description="Introuvable")
     * )
     */
    public function update(UpdateInventoryMovementRequest $request, InventoryMovement $movement): JsonResponse
    {
        try {
            $this->authorize('update', $movement);

            $updated = $this->inventoryMovementService->update($movement, $request->validated());

            return ApiService::response(new InventoryMovementResource($updated), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Inventory movement not found', 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/inventory-movements/{movement}",
     *     tags={"Inventory"},
     *     summary="Supprimer un mouvement",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="movement", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Mouvement supprimé"),
     *     @OA\Response(response=404, description="Introuvable")
     * )
     */
    public function destroy(InventoryMovement $movement): JsonResponse
    {
        try {
            $this->authorize('delete', $movement);

            $this->inventoryMovementService->delete($movement);

            return ApiService::response(['message' => 'Inventory movement deleted'], 200);
        } catch (\Throwable $e) {
            return ApiService::response('Inventory movement not found', 404);
        }
    }
}
