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

class StoreCategoryController extends Controller
{
    public function __construct(private StoreCategoryService $service)
    {
    }

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
