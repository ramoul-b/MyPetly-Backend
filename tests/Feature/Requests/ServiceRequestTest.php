<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_service_request_passes_validation_when_authorized(): void
    {
        $category = Category::create([
            'name'  => ['en' => 'Vet'],
            'icon'  => 'icon',
            'type'  => 'service',
            'color' => '#ffffff',
        ]);
        $provider = Provider::factory()->create();

        $data = [
            'category_id' => $category->id,
            'provider_id' => $provider->id,
            'name'        => ['en' => 'Service'],
            'description' => ['en' => 'Desc'],
            'icon'        => 'mdi-vet',
            'color'       => '#ff0000',
            'price'       => 10,
            'active'      => true,
        ];

        $request = new StoreServiceRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_update_service_request_passes_validation_when_authorized(): void
    {
        $category = Category::create([
            'name'  => ['en' => 'Vet'],
            'icon'  => 'icon',
            'type'  => 'service',
            'color' => '#ffffff',
        ]);
        $provider = Provider::factory()->create();

        $data = [
            'category_id' => $category->id,
            'provider_id' => $provider->id,
            'name'        => ['en' => 'Service'],
            'description' => ['en' => 'Desc'],
            'icon'        => 'mdi-vet',
            'color'       => '#00ff00',
            'price'       => 20,
            'active'      => false,
        ];

        $request = new UpdateServiceRequest();

        $this->assertTrue($request->authorize());
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }
}
