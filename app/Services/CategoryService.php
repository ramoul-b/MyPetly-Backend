<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getAll(): Collection
    {
        return Category::all();
    }

    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        $category = new Category();
        $category->setTranslations('name', $data['name']);
        $category->icon = $data['icon'] ?? null;
        $category->type = $data['type'] ?? null;
        $category->color = $data['color'] ?? null;
        $category->save();

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['name'])) {
            $category->setTranslations('name', $data['name']);
        }
        if (isset($data['icon'])) {
            $category->icon = $data['icon'];
        }
        if (isset($data['type'])) {
            $category->type = $data['type'];
        }
        if (isset($data['color'])) {
            $category->color = $data['color'];
        }
        $category->save();

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
