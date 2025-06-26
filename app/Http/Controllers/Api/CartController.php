<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Models\CartItem;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Tag(name="Cart", description="Gestion du panier")
 */
class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Liste des items du panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste du panier")
     * )
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', CartItem::class);
        $items = CartItem::with('product')->where('user_id', Auth::id())->get();
        return ApiService::response(CartResource::collection($items), 200);
    }

    /**
     * @OA\Post(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Ajouter au panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"product_id","quantity"},
     *         @OA\Property(property="product_id", type="integer"),
     *         @OA\Property(property="quantity", type="integer")
     *     )),
     *     @OA\Response(response=201, description="Ajouté")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', CartItem::class);
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        $item = $this->cartService->addToCart($data);
        return ApiService::response(new CartResource($item), 201);
    }

    /**
     * @OA\Delete(
     *     path="/cart/{id}",
     *     tags={"Cart"},
     *     summary="Retirer un item",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Supprimé")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $item = CartItem::findOrFail($id);
        $this->authorize('delete', $item);
        $this->cartService->remove($item);
        return ApiService::response(['message' => 'Deleted'], 200);

    /**
     * @OA\Delete(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Vider le panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Panier vidé")
     * )
     */
    public function clear(): JsonResponse
    {
        $this->authorize('delete', CartItem::class);
        $this->cartService->clear();
        return ApiService::response(['message' => 'Cleared'], 200);

    }
}
