<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\ApiService;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;

class CategoryController extends Controller
{
/**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Obtenir toutes les catégories",
     *     description="Récupère la liste de toutes les catégories disponibles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories récupérée avec succès",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage", "en": "Grooming", "es": "Aseo"}
     *             ),
     *             @OA\Property(property="type", type="string", example="service"),
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
        try {
            $this->authorize('view', new Category());
            $categories = Category::all();
            return ApiService::response(CategoryResource::collection($categories), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la récupération des catégories.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Créer une catégorie",
     *     description="Ajoute une nouvelle catégorie avec support multilingue",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage", "en": "Grooming", "es": "Aseo"}
     *             ),
     *             @OA\Property(property="type", type="string", example="service"),
     *             @OA\Property(property="icon", type="string", example="icon.png"),
     *             @OA\Property(property="color", type="string", example="#ffcc00")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catégorie créée avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Category());
            $category = Category::create($request->validated());
            return ApiService::response(new CategoryResource($category), 201);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la création de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Obtenir les détails d'une catégorie",
     *     description="Récupère les détails d'une catégorie spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage", "en": "Grooming", "es": "Aseo"}
     *             ),
     *             @OA\Property(property="type", type="string", example="service"),
     *             @OA\Property(property="icon", type="string", example="icon.png"),
     *             @OA\Property(property="color", type="string", example="#ffcc00"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-19 10:30"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-19 11:00")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Catégorie non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function show(Category $category): JsonResponse
    {
        try {
            $this->authorize('view', $category);
            return ApiService::response(new CategoryResource($category), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
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
            $this->authorize('update', $category);
            $category->update($request->validated());
            return ApiService::response(new CategoryResource($category), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la mise à jour de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Supprimer une catégorie",
     *     description="Supprime une catégorie par ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie supprimée avec succès"),
     *     @OA\Response(response=404, description="Catégorie non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->authorize('delete', $category);
            $category->delete();
            return ApiService::response(['message' => 'Catégorie supprimée avec succès.'], 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Erreur lors de la suppression de la catégorie.', 'error' => $e->getMessage()], 500);
        }
    }
}
