<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ApiService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Products", description="Gestion des produits")
 */
class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *     path="/products/by-user/{userId}",
     *     tags={"Products"},
     *     summary="Liste des produits par utilisateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des produits récupérée"),
     *     @OA\Response(response=404, description="Aucun produit trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getByUserId(int $userId): JsonResponse
    {
        try {
            $this->authorize('view', new Product());
            $products = $this->productService->getByUserId($userId);
            if ($products->isEmpty()) {
                return ApiService::response('Products not found', 404);
            }
            return ApiService::response(ProductResource::collection($products), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/my/low-stock",
     *     tags={"Products"},
     *     summary="Liste des produits avec stock faible pour le fournisseur courant",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="threshold", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Response(response=200, description="Liste récupérée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getMyLowStockProducts(Request $request): JsonResponse
    {
        try {
            $this->authorize('view', new Product());
            $threshold = (int) $request->query('threshold', 10);
            $limit = (int) $request->query('limit', 10);
            $userId = $request->user()->id;

            $products = $this->productService->getLowStockByUser($userId, $threshold, $limit);

            $data = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->getTranslation('name', app()->getLocale()),
                    'sku' => $product->sku ?? null,
                    'stock' => $product->stock,
                    'parentId' => optional($product->category)->id,
                    'parentTitle' => optional($product->category)->getTranslation('name', app()->getLocale()),
                ];
            });

            return ApiService::response($data, 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Liste des produits",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste récupérée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new Product());
            $products = Product::with(['category', 'store'])->get();
            return ApiService::response(ProductResource::collection($products), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Créer un produit",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="store_id", type="integer", example=1),
     *         @OA\Property(property="product_category_id", type="integer", example=1),
     *         @OA\Property(property="name", type="object", example={"fr":"Produit"}),
     *         @OA\Property(property="description", type="object", example={"fr":"Description"})
     *     )),
     *     @OA\Response(response=201, description="Produit créé"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Product());
            $product = $this->productService->create($request->validated());
            return ApiService::response(new ProductResource($product), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Afficher un produit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails du produit"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'store'])->findOrFail($id);
            $this->authorize('view', $product);
            return ApiService::response(new ProductResource($product), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Product not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Mettre à jour un produit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Produit mis à jour"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);
            $updated = $this->productService->update($product, $request->validated());
            return ApiService::response(new ProductResource($updated), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Product not found', 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Supprimer un produit",
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
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);
            $this->productService->delete($product);
            return ApiService::response(['message' => 'Product deleted'], 200);
        } catch (\Throwable $e) {
            return ApiService::response('Product not found', 404);
        }
    }
}

