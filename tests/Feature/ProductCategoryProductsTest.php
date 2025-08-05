<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_products_for_category(): void
    {
        $category = ProductCategory::factory()->create();
        $products = Product::factory()->count(2)->create([
            'product_category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/v1/product-categories/{$category->id}/products");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['id' => $products[0]->id])
            ->assertJsonFragment(['id' => $products[1]->id]);
    }
}
