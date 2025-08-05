<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_product_request_passes_validation_when_authorized(): void
    {
        $store = Store::factory()->create();
        $category = ProductCategory::create([
            'name' => ['en' => 'Cat'],
            'description' => ['en' => 'Desc'],
            'icon' => 'icon',
            'color' => '#ffffff',
        ]);

        $data = [
            'store_id' => $store->id,
            'product_category_id' => $category->id,
            'name' => ['en' => 'Product'],
            'description' => ['en' => 'Desc'],
            'price' => 10,
            'stock' => 5,
            'image' => 'img.jpg',
        ];

        $request = new StoreProductRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_update_product_request_passes_validation_when_authorized(): void
    {
        $store = Store::factory()->create();
        $category = ProductCategory::create([
            'name' => ['en' => 'Cat'],
            'description' => ['en' => 'Desc'],
            'icon' => 'icon',
            'color' => '#ffffff',
        ]);

        $data = [
            'product_category_id' => $category->id,
            'store_id' => $store->id,
            'name' => ['en' => 'Product'],
            'description' => ['en' => 'Desc'],
            'price' => 10,
            'stock' => 5,
            'image' => 'img.jpg',
        ];

        $request = new UpdateProductRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }
}
