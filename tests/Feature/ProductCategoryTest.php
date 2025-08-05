<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    private array $permissions = [
        'view_any_product_category',
        'create_product_category',
        'edit_any_product_category',
        'delete_any_product_category',
        'manage product categories',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        foreach ($this->permissions as $perm) {
            Permission::create(['name' => $perm]);
        }
    }

    public function test_product_category_crud_endpoints(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo($this->permissions);
        Sanctum::actingAs($user);

        $createData = [
            'name' => ['en' => 'Food'],
            'description' => ['en' => 'desc'],
        ];

        $response = $this->postJson('/api/v1/product-categories', $createData);
        $response->assertStatus(201)->assertJsonPath('name.en', 'Food');
        $categoryId = $response->json('id');

        $this->getJson('/api/v1/product-categories')
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $categoryId]);

        $this->getJson("/api/v1/product-categories/{$categoryId}")
            ->assertStatus(200)
            ->assertJsonPath('id', $categoryId);

        $updateData = [
            'name' => ['en' => 'Updated'],
            'description' => ['en' => 'updated'],
        ];

        $this->putJson("/api/v1/product-categories/{$categoryId}", $updateData)
            ->assertStatus(200)
            ->assertJsonPath('name.en', 'Updated');

        $this->deleteJson("/api/v1/product-categories/{$categoryId}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('product_categories', ['id' => $categoryId]);
    }

    public function test_store_product_category_validation_error(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo($this->permissions);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/product-categories', [
            'name' => 'invalid',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_product_category_requires_manage_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo([
            'view_any_product_category',
            'create_product_category',
            'edit_any_product_category',
            'delete_any_product_category',
        ]);
        Sanctum::actingAs($user);

        $category = ProductCategory::create([
            'name' => ['en' => 'Food'],
        ]);

        $this->putJson("/api/v1/product-categories/{$category->id}", [
            'name' => ['en' => 'Updated'],
        ])->assertStatus(403);
    }

    public function test_public_can_view_product_categories_without_token(): void
    {
        $category = ProductCategory::create([
            'name' => ['en' => 'Food'],
        ]);

        $this->getJson('/api/v1/product-categories')
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $category->id]);
    }

    public function test_public_can_view_single_product_category_without_token(): void
    {
        $category = ProductCategory::create([
            'name' => ['en' => 'Food'],
        ]);

        $this->getJson("/api/v1/product-categories/{$category->id}")
            ->assertStatus(200)
            ->assertJsonPath('id', $category->id);
    }
}

