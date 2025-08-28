<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OrdersMyTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/orders/my');
        $response->assertStatus(401);
    }

    public function test_filters_and_paginates_orders_for_provider(): void
    {
        $provider = User::factory()->create();
        Permission::create(['name' => 'view_own_order']);
        $provider->givePermissionTo('view_own_order');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $store = Store::factory()->create(['user_id' => $provider->id]);

        $pendingOld = Order::create([
            'user_id' => $provider->id,
            'store_id' => $store->id,
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_status' => 'pending',
        ]);
        $pendingOld->forceFill(['created_at' => now()->subDays(2)])->save();

        $pendingNew = Order::create([
            'user_id' => $provider->id,
            'store_id' => $store->id,
            'total' => 200,
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_status' => 'pending',
        ]);
        $pendingNew->forceFill(['created_at' => now()->subDay()])->save();

        $completed = Order::create([
            'user_id' => $provider->id,
            'store_id' => $store->id,
            'total' => 300,
            'status' => 'completed',
            'payment_status' => 'pending',
            'shipping_status' => 'pending',
        ]);

        $otherProvider = User::factory()->create();
        $otherStore = Store::factory()->create(['user_id' => $otherProvider->id]);
        $otherOrder = Order::create([
            'user_id' => $otherProvider->id,
            'store_id' => $otherStore->id,
            'total' => 150,
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_status' => 'pending',
        ]);

        $response = $this->actingAs($provider, 'sanctum')
            ->getJson('/api/v1/orders/my?status=pending&sort=-date&limit=1&page=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $pendingNew->id)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.page', 1)
            ->assertJsonPath('meta.total_pages', 2)
            ->assertJsonMissing(['id' => $completed->id])
            ->assertJsonMissing(['id' => $otherOrder->id]);
    }
}

