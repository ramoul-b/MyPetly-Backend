<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\ApiService;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Liste toutes les catégories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des catégories récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Category::all();
            return ApiService::response(CategoryResource::collection($categories), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération des catégories.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Crée une nouvelle catégorie",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="Toilettage"),
     *         @OA\Property(property="icon", type="string", example="spa"),
     *         @OA\Property(property="type", type="string", example="material"),
     *         @OA\Property(property="color", type="string", example="#FF0000")
     *     )),
     *     @OA\Response(response=201, description="Catégorie créée avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());
            return ApiService::response(new CategoryResource($category), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la création de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Affiche une catégorie spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie récupérée avec succès"),
     *     @OA\Response(response=404, description="Catégorie introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function show(Category $category): JsonResponse
    {
        try {
            return ApiService::response(new CategoryResource($category), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Met à jour une catégorie existante",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="Toilettage Deluxe"),
     *         @OA\Property(property="icon", type="string", example="spa"),
     *         @OA\Property(property="type", type="string", example="material"),
     *         @OA\Property(property="color", type="string", example="#00FF00")
     *     )),
     *     @OA\Response(response=200, description="Catégorie mise à jour avec succès"),
     *     @OA\Response(response=404, description="Catégorie introuvable"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category->update($request->validated());
            return ApiService::response(new CategoryResource($category), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la mise à jour de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Supprime une catégorie",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie supprimée avec succès"),
     *     @OA\Response(response=404, description="Catégorie introuvable"),
     *     @OA\Response(response=500, description="Erreur serveur interne")
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
            return ApiService::response(['message' => 'Catégorie supprimée avec succès.'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la suppression de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }
}
