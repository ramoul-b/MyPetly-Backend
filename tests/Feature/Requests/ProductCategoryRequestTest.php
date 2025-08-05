<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

use Tests\TestCase;

class ProductCategoryRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'create_product_category']);
        Permission::create(['name' => 'edit_any_product_category']);
    }

    public function test_store_product_category_request_authorizes_when_user_has_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_product_category');
        $this->actingAs($user);

        $request = new StoreProductCategoryRequest();

        $this->assertTrue($request->authorize());

        $data = [
            'name' => ['en' => 'Category'],
            'description' => ['en' => 'Desc'],
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_store_product_category_request_fails_authorization_without_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new StoreProductCategoryRequest();

        $this->assertFalse($request->authorize());
    }

    public function test_store_product_category_request_fails_validation(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_product_category');
        $this->actingAs($user);

        $request = new StoreProductCategoryRequest();

        $data = [
            'name' => 'invalid',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }

    public function test_update_product_category_request_authorizes_when_user_has_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_any_product_category');
        $this->actingAs($user);

        $request = new UpdateProductCategoryRequest();

        $this->assertTrue($request->authorize());

        $data = [
            'name' => ['en' => 'Category'],
            'description' => ['en' => 'Desc'],
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_update_product_category_request_fails_authorization_without_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new UpdateProductCategoryRequest();

        $this->assertFalse($request->authorize());
    }

    public function test_update_product_category_request_fails_validation(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_any_product_category');
        $this->actingAs($user);

        $request = new UpdateProductCategoryRequest();

        $data = [
            'name' => 'invalid',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }
}

