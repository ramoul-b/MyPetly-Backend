<?php

namespace Tests\Feature;

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
}

