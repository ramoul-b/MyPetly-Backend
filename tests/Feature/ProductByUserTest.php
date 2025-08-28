<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductByUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/products/by-user/{$user->id}");

        $response->assertStatus(401);
    }

    public function test_returns_products_for_user(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'view_any_product']);
        $user->givePermissionTo('view_any_product');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $products = Product::factory()->count(2)->create(['store_id' => $store->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/products/by-user/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['id' => $products[0]->id])
            ->assertJsonFragment(['id' => $products[1]->id]);
    }

    public function test_returns_404_when_no_products(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'view_any_product']);
        $user->givePermissionTo('view_any_product');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Store::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/products/by-user/{$user->id}");

        $response->assertStatus(404);
    }
}
