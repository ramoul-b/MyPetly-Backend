<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function create(array $data): Product
    {
        $product = new Product();
        $product->product_category_id = $data['product_category_id'];
        $product->store_id = $data['store_id'];
        $product->price = $data['price'];
        $product->stock = $data['stock'];
        $product->image = $data['image'] ?? null;
        $product->status = $data['status'] ?? 'active';
        $product->setTranslations('name', $data['name']);
        $product->setTranslations('description', $data['description'] ?? []);
        $product->save();
        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->product_category_id = $data['product_category_id'];
        if (isset($data['store_id'])) {
            $product->store_id = $data['store_id'];
        }
        $product->price = $data['price'];
        $product->stock = $data['stock'];
        $product->image = $data['image'] ?? null;
        if (isset($data['status'])) {
            $product->status = $data['status'];
        }
        $product->setTranslations('name', $data['name']);
        $product->setTranslations('description', $data['description'] ?? []);
        $product->save();
        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}

