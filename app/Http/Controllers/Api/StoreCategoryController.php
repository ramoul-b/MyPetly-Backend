<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreCategory;
use App\Http\Resources\StoreCategoryResource;
use App\Services\ApiService;
use App\Services\StoreCategoryService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @OA\Tag(name="Store Categories", description="Gestion des catégories de boutique")
 */
class StoreCategoryController extends Controller
{
    public function __construct(private StoreCategoryService $service)
    {
    }

    /**
     * @OA\Get(
     *     path="/store/categories/my",
     *     tags={"Store Categories"},
     *     summary="Liste des catégories de la boutique de l'utilisateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="flat", in="query", required=false, @OA\Schema(type="boolean"), description="Retourner la liste à plat"),
     *     @OA\Response(response=200, description="Liste récupérée"),
     *     @OA\Response(response=404, description="Boutique introuvable"),
     *     @OA\Response(response=500, description="Erreur lors de la récupération")
     * )
     */
    public function my(Request $request): JsonResponse
    {
        try {
            $store = Store::where('user_id', $request->user()->id)->first();
            if (!$store) {
                return ApiService::response(['message' => 'Store not found'], 404);
            }

            $this->authorize('view', $store);

            $flat = $request->boolean('flat', true);
            $categories = $this->service->listForStore($store, $flat);

            return ApiService::response(StoreCategoryResource::collection($categories), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Error retrieving categories', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/store/categories/{id}",
     *     tags={"Store Categories"},
     *     summary="Afficher une catégorie",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie trouvée"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=500, description="Erreur lors de la récupération")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = StoreCategory::find($id);
            if (!$category) {
                return ApiService::response(['message' => 'Category not found'], 404);
            }

            $this->authorize('view', $category);

            return ApiService::response(new StoreCategoryResource($category), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Error retrieving category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/store/categories",
     *     tags={"Store Categories"},
     *     summary="Créer une catégorie",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="object", example={"fr":"Catégorie"}),
     *         @OA\Property(property="slug", type="string", example="categorie"),
     *         @OA\Property(property="parent_id", type="integer", example=1)
     *     )),
     *     @OA\Response(response=201, description="Catégorie créée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=404, description="Boutique introuvable"),
     *     @OA\Response(response=500, description="Erreur lors de la création")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $store = Store::where('user_id', $request->user()->id)->first();
            if (!$store) {
                return ApiService::response(['message' => 'Store not found'], 404);
            }

            $this->authorize('create', new StoreCategory());

            $validator = Validator::make($request->all(), [
                'name' => 'required|array',
                'name.*' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:store_categories,id',
            ]);

            if ($validator->fails()) {
                return ApiService::response($validator->errors(), 422);
            }

            $data = $validator->validated();
            $data['store_id'] = $store->id;
            if (!isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']['en'] ?? reset($data['name']));
            }

            $category = StoreCategory::create($data);

            return ApiService::response(new StoreCategoryResource($category), 201);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Error creating category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/store/categories/{id}",
     *     tags={"Store Categories"},
     *     summary="Mettre à jour une catégorie",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=false, @OA\JsonContent(
     *         @OA\Property(property="name", type="object", example={"fr":"Catégorie"}),
     *         @OA\Property(property="slug", type="string", example="categorie"),
     *         @OA\Property(property="parent_id", type="integer", example=1)
     *     )),
     *     @OA\Response(response=200, description="Catégorie mise à jour"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur lors de la mise à jour")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $category = StoreCategory::find($id);
            if (!$category) {
                return ApiService::response(['message' => 'Category not found'], 404);
            }

            $this->authorize('update', $category);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|array',
                'name.*' => 'required_with:name|string|max:255',
                'slug' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:store_categories,id',
            ]);

            if ($validator->fails()) {
                return ApiService::response($validator->errors(), 422);
            }

            $data = $validator->validated();
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']['en'] ?? reset($data['name']));
            }

            $category->update($data);

            return ApiService::response(new StoreCategoryResource($category), 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Error updating category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/store/categories/{id}",
     *     tags={"Store Categories"},
     *     summary="Supprimer une catégorie",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie supprimée"),
     *     @OA\Response(response=404, description="Introuvable"),
     *     @OA\Response(response=409, description="La catégorie possède des sous-catégories"),
     *     @OA\Response(response=500, description="Erreur lors de la suppression")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $category = StoreCategory::find($id);
            if (!$category) {
                return ApiService::response(['message' => 'Category not found'], 404);
            }

            $this->authorize('delete', $category);

            if ($category->children()->exists()) {
                return ApiService::response(['message' => 'Category has children'], 409);
            }

            $category->delete();

            return ApiService::response(['message' => 'Category deleted'], 200);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Error deleting category', 'error' => $e->getMessage()], 500);
        }
    }
}
