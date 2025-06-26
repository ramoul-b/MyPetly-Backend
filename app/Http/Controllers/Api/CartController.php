<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Services\ApiService;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Cart", description="Gestion du panier")
 */
class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Lister les articles du panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des articles")
     * )
     */
    public function index(): JsonResponse
    {
        $items = CartItem::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return ApiService::response(CartResource::collection($items), 200);
    }

    /**
     * @OA\Post(
     *     path="/cart/add",
     *     tags={"Cart"},
     *     summary="Ajouter un article au panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="product_id", type="integer", example=1),
     *         @OA\Property(property="quantity", type="integer", example=2)
     *     )),
     *     @OA\Response(response=201, description="Article ajouté")
     * )
     */
    public function add(StoreCartItemRequest $request): JsonResponse
    {
        $item = $this->cartService->addToCart($request->validated());
        $item->load('product');

        return ApiService::response(new CartResource($item), 201);
    }

    /**
     * @OA\Put(
     *     path="/cart/update/{item}",
     *     tags={"Cart"},
     *     summary="Mettre à jour un article du panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="item", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="quantity", type="integer", example=1)
     *     )),
     *     @OA\Response(response=200, description="Article mis à jour")
     * )
     */
    public function update(UpdateCartItemRequest $request, CartItem $item): JsonResponse
    {
        $item->update(['quantity' => $request->quantity]);
        $item->load('product');

        return ApiService::response(new CartResource($item), 200);
    }

    /**
     * @OA\Delete(
     *     path="/cart/remove/{item}",
     *     tags={"Cart"},
     *     summary="Retirer un article du panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="item", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Article supprimé")
     * )
     */
    public function remove(CartItem $item): JsonResponse
    {
        $this->cartService->remove($item);
        return ApiService::response(['message' => 'Item removed'], 200);
    }
}
