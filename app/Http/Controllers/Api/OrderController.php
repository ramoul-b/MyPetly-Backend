<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\ApiService;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Orders", description="Gestion des commandes")
 */
class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Lister les commandes",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des commandes",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="total", type="number", format="float", example=99.99),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-19 10:30:00")
     *         ))
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new Order());
            $orders = $this->orderService->list();
            return ApiService::response(OrderResource::collection($orders), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Afficher une commande",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la commande",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="total", type="number", format="float", example=99.99),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-19 10:30:00")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=404, description="Commande introuvable"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->find($id);
            $this->authorize('view', $order);
            return ApiService::response(new OrderResource($order), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Order not found', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/orders/checkout",
     *     tags={"Orders"},
     *     summary="Créer une commande",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="shipping_address", type="string", example="1 rue de Paris"),
     *             @OA\Property(property="billing_address", type="string", example="1 rue de Paris")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Commande créée",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="total", type="number", format="float", example=99.99)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Order());
            $order = $this->orderService->checkout($request->validated());
            return ApiService::response(new OrderResource($order), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/my/stats",
     *    tags={"Orders"},
     *    summary="Statistiques des commandes du fournisseur",
     *    security={{"bearerAuth":{}}},
     *    @OA\Parameter(
     *        name="date_from",
     *        in="query",
     *        required=false,
     *        @OA\Schema(type="string", format="date", example="2025-01-01")
     *    ),
     *    @OA\Parameter(
     *        name="date_to",
     *        in="query",
     *        required=false,
     *        @OA\Schema(type="string", format="date", example="2025-01-31")
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Statistiques calculées",
     *        @OA\JsonContent(
     *            @OA\Property(property="total_revenue", type="number", format="float", example=1500.50),
     *            @OA\Property(property="order_count", type="integer", example=10),
     *            @OA\Property(property="avg_order_value", type="number", format="float", example=150.05)
     *        )
     *    ),
     *    @OA\Response(response=401, description="Non authentifié"),
     *    @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $this->authorize('view', new Order());
            $stats = $this->orderService->statsForProvider(
                $request->user(),
                $request->query('date_from'),
                $request->query('date_to')
            );
            return ApiService::response($stats, 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }
}
