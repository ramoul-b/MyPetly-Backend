<?php

namespace App\Services;

use App\Models\ProductCategory;

class ProductCategoryService
{
    public function create(array $data): ProductCategory
    {
        $category = new ProductCategory();
        $category->setTranslations('name', $data['name']);
        $category->setTranslations('description', $data['description'] ?? []);
        $category->save();
        return $category;
    }

    public function update(ProductCategory $category, array $data): ProductCategory
    {
        $category->setTranslations('name', $data['name']);
        $category->setTranslations('description', $data['description'] ?? []);
        $category->save();
        return $category;
    }

    public function delete(ProductCategory $category): void
    {
        $category->delete();
    }
}

