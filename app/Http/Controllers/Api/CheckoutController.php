<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Services\ApiService;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Checkout", description="Finaliser la commande")
 */
class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService, private CartService $cartService) {}

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

        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return ApiService::response('Cart is empty', 400);
        }

        $data = [
            'items' => $cartItems->map(fn($i) => [
                'product_id' => $i->product_id,
                'quantity' => $i->quantity,
                'unit_price' => $i->product->price,
            ])->toArray(),
            'shipping_address' => $request->input('shipping_address'),
            'billing_address' => $request->input('billing_address'),
        ];

        $order = $this->orderService->create($data);
        $this->cartService->clear();

        return ApiService::response(new OrderResource($order), 201);
    }
}
