<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreCategory;
use Illuminate\Database\Eloquent\Collection;

class StoreCategoryService
{
    public function listForStore(Store $store, bool $flat = true): Collection
    {
        $categories = StoreCategory::where('store_id', $store->id)->get();

        if ($flat) {
            return $categories;
        }

        $grouped = $categories->groupBy('parent_id');

        $buildTree = function ($parentId) use (&$buildTree, $grouped) {
            return $grouped->get($parentId, collect())->map(function ($category) use (&$buildTree) {
                $category->children = $buildTree($category->id);
                return $category;
            });
        };

        return $buildTree(null);
    }
}
