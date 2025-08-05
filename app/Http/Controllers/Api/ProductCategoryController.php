<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\ProductCategory;
use App\Services\ApiService;
use App\Services\ProductCategoryService;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    public function __construct(private ProductCategoryService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/product-categories",
     *     tags={"Product Categories"},
     *     summary="Obtenir toutes les catégories de produit",
     *     description="Récupère la liste de toutes les catégories de produit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories de produit récupérée avec succès",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object", example={"fr": "Accessoires", "en": "Accessories", "es": "Accesorios"}),
     *             @OA\Property(property="description", type="object", example={"fr": "Description", "en": "Description"}),
     *             @OA\Property(property="icon", type="string", example="icon.png"),
     *             @OA\Property(property="color", type="string", example="#ffcc00"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-19 10:30"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-19 11:00")
     *         ))
     *     ),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function index(): JsonResponse
    {
        $categories = ProductCategory::all();
        return ApiService::response(ProductCategoryResource::collection($categories), 200);
    }

    /**
     * @OA\Post(
     *     path="/product-categories",
     *     tags={"Product Categories"},
     *     summary="Créer une catégorie de produit",
     *     description="Ajoute une nouvelle catégorie de produit avec support multilingue",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="object", example={"fr": "Accessoires", "en": "Accessories", "es": "Accesorios"}),
     *             @OA\Property(property="description", type="object", example={"fr": "Pour animaux", "en": "For pets"}),
     *             @OA\Property(property="icon", type="string", example="icon.png"),
     *             @OA\Property(property="color", type="string", example="#ffcc00")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catégorie de produit créée avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', new ProductCategory());
        $validated = $request->validated();
        $category = $this->service->create($validated);
        return ApiService::response(new ProductCategoryResource($category), 201);
    }

    /**
     * @OA\Get(
     *     path="/product-categories/{id}",
     *     tags={"Product Categories"},
     *     summary="Obtenir les détails d'une catégorie de produit",
     *     description="Récupère les détails d'une catégorie de produit spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object", example={"fr": "Accessoires", "en": "Accessories", "es": "Accesorios"}),
     *             @OA\Property(property="description", type="object", example={"fr": "Description", "en": "Description"}),
     *             @OA\Property(property="icon", type="string", example="icon.png"),
     *             @OA\Property(property="color", type="string", example="#ffcc00"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-19 10:30"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-19 11:00")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Catégorie de produit non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function show(ProductCategory $productCategory): JsonResponse
    {
        return ApiService::response(new ProductCategoryResource($productCategory), 200);
    }

    /**
     * @OA\Get(
     *     path="/product-categories/{id}/products",
     *     tags={"Product Categories"},
     *     summary="Obtenir les produits d'une catégorie",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des produits récupérée avec succès")
     * )
     */
    public function products(ProductCategory $productCategory): JsonResponse
    {
        $products = $productCategory->products()->with(['category', 'store'])->get();
        return ApiService::response(ProductResource::collection($products), 200);
    }

    /**
     * @OA\Put(
     *     path="/product-categories/{id}",
     *     tags={"Product Categories"},
     *     summary="Met à jour une catégorie de produit existante",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="object", example={"fr": "Accessoires modifiés", "en": "Updated accessories"}),
     *        @OA\Property(property="description", type="object", example={"fr": "Description mise à jour", "en": "Updated description"}),
     *         @OA\Property(property="icon", type="string", example="new-icon.png"),
     *         @OA\Property(property="color", type="string", example="#00FF00")
     *     )),
     *     @OA\Response(response=200, description="Catégorie de produit mise à jour avec succès"),
     *     @OA\Response(response=404, description="Catégorie de produit introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        $this->authorize('update', $productCategory);
        $validated = $request->validated();
        $updated = $this->service->update($productCategory, $validated);
        return ApiService::response(new ProductCategoryResource($updated), 200);
    }

    /**
     * @OA\Delete(
     *     path="/product-categories/{id}",
     *     tags={"Product Categories"},
     *     summary="Supprimer une catégorie de produit",
     *     description="Supprime une catégorie de produit par ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie de produit supprimée avec succès"),
     *     @OA\Response(response=404, description="Catégorie de produit non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        $this->authorize('delete', $productCategory);
        $this->service->delete($productCategory);
        return ApiService::response(['message' => 'Product category deleted'], 200);
    }
}

