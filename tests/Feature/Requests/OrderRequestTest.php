<?php

namespace Tests\Feature;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class OrderRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_order_request_passes_validation_when_authorized(): void
    {
        $product = Product::factory()->create();

        $data = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $request = new StoreOrderRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }
}
