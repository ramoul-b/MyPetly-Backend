<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductCategoryRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_product_category_request_passes_validation_when_authorized(): void
    {
        $data = [
            'name' => ['en' => 'Food'],
            'description' => ['en' => 'Desc'],
            'icon' => 'icon',
            'color' => '#abcdef',
        ];

        $request = new StoreProductCategoryRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_update_product_category_request_passes_validation_when_authorized(): void
    {
        $data = [
            'name' => ['en' => 'Food'],
            'description' => ['en' => 'Desc'],
            'icon' => 'icon',
            'color' => '#abcdef',
        ];

        $request = new UpdateProductCategoryRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }
}
