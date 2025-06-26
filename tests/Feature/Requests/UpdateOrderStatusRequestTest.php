<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\UpdateOrderStatusRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateOrderStatusRequestTest extends TestCase
{
    public function test_update_order_status_request_passes_validation(): void
    {
        $data = ['shipping_status' => 'shipped'];

        $request = new UpdateOrderStatusRequest();
        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }
}
