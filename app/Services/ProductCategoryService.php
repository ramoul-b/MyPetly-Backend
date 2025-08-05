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
        $category->icon = $data['icon'] ?? null;
        $category->color = $data['color'] ?? null;
        $category->save();
        return $category;
    }

    public function update(ProductCategory $category, array $data): ProductCategory
    {
        $category->setTranslations('name', $data['name']);
        $category->setTranslations('description', $data['description'] ?? []);
        if (array_key_exists('icon', $data)) {
            $category->icon = $data['icon'];
        }
        if (array_key_exists('color', $data)) {
            $category->color = $data['color'];
        }
        $category->save();
        return $category;
    }

    public function delete(ProductCategory $category): void
    {
        $category->delete();
    }
}

