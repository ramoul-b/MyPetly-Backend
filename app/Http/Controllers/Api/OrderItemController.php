<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderItemResource;
use App\Models\Order;
use App\Services\ApiService;
use App\Services\OrderItemService;
use Illuminate\Http\JsonResponse;

class OrderItemController extends Controller
{
    public function __construct(private OrderItemService $orderItemService) {}

    /**
     * @OA\Get(
     *     path="/orders/{order}/items",
     *     tags={"Orders"},
     *     summary="Liste les items d’une commande",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des items"),
     *     @OA\Response(response=404, description="Commande introuvable")
     * )
     */
    public function index(Order $order): JsonResponse
    {
        try {
            $this->authorize('view', $order);
            $items = $this->orderItemService->listByOrder($order);
            return ApiService::response(OrderItemResource::collection($items), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Order not found', 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/order-items/{id}",
     *     tags={"Orders"},
     *     summary="Voir un item de commande",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item trouvé"),
     *     @OA\Response(response=404, description="Item introuvable")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $item = $this->orderItemService->find($id);
            $this->authorize('view', $item->order);
            return ApiService::response(new OrderItemResource($item), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Order item not found', 404);
        }
    }
}
