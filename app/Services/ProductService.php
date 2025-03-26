<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function create(array $data): Product
    {
        $product = new Product();
        $product->product_category_id = $data['product_category_id'];
        $product->price = $data['price'];
        $product->stock = $data['stock'];
        $product->image = $data['image'] ?? null;
        $product->setTranslations('name', $data['name']);
        $product->setTranslations('description', $data['description'] ?? []);
        $product->save();
        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->product_category_id = $data['product_category_id'];
        $product->price = $data['price'];
        $product->stock = $data['stock'];
        $product->image = $data['image'] ?? null;
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
