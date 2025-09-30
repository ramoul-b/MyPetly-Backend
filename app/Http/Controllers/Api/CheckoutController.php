<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\ApiService;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Checkout", description="Finaliser la commande")
 */
class CheckoutController extends Controller
{
    public function __construct(private CartService $cartService) {}

    /**
     * @OA\Post(
     *     path="/checkout",
     *     tags={"Checkout"},
     *     summary="Créer une commande depuis le panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="shipping_address", type="string"),
     *         @OA\Property(property="billing_address", type="string")
     *     )),
     *     @OA\Response(response=201, description="Commande créée")
     * )
     */
    public function checkout(Request $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        try {
            $order = $this->cartService->checkout($request->only([
                'shipping_address',
                'billing_address',
            ]));
        } catch (\RuntimeException $e) {
            return ApiService::response($e->getMessage(), 400);
        }

        return ApiService::response(new OrderResource($order), 201);
    }
}
