<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Services\ApiService;
use App\Services\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function __construct(private ProductCategoryService $service)
    {
    }

    public function index(): JsonResponse
    {
        $this->authorize('view', new ProductCategory());
        $categories = ProductCategory::all();
        return ApiService::response(ProductCategoryResource::collection($categories), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', new ProductCategory());
        $validated = $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
        ]);
        $category = $this->service->create($validated);
        return ApiService::response(new ProductCategoryResource($category), 201);
    }

    public function show(ProductCategory $productCategory): JsonResponse
    {
        $this->authorize('view', $productCategory);
        return ApiService::response(new ProductCategoryResource($productCategory), 200);
    }

    public function update(Request $request, ProductCategory $productCategory): JsonResponse
    {
        $this->authorize('update', $productCategory);
        $validated = $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
        ]);
        $updated = $this->service->update($productCategory, $validated);
        return ApiService::response(new ProductCategoryResource($updated), 200);
    }

    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        $this->authorize('delete', $productCategory);
        $this->service->delete($productCategory);
        return ApiService::response(['message' => 'Product category deleted'], 200);
    }
}

