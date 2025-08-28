<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class OrdersMyStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_enforcement(): void
    {
        $response = $this->getJson('/api/v1/orders/my/stats');

        $response->assertStatus(401);
    }

    public function test_returns_stats_without_date_filters(): void
    {
        $provider = User::factory()->create();
        Permission::create(['name' => 'view_own_order']);
        $provider->givePermissionTo('view_own_order');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $store = Store::factory()->create(['user_id' => $provider->id]);
        $customer = User::factory()->create();

        Order::create(['user_id' => $customer->id, 'store_id' => $store->id, 'total' => 100]);
        Order::create(['user_id' => $customer->id, 'store_id' => $store->id, 'total' => 200]);

        $otherStore = Store::factory()->create();
        Order::create(['user_id' => $customer->id, 'store_id' => $otherStore->id, 'total' => 300]);

        $response = $this->actingAs($provider, 'sanctum')
            ->getJson('/api/v1/orders/my/stats');

        $response->assertStatus(200)
            ->assertExactJson([
                'total_revenue' => 300,
                'order_count' => 2,
                'avg_order_value' => 150,
            ]);
    }

    public function test_returns_stats_with_date_filters(): void
    {
        $provider = User::factory()->create();
        Permission::create(['name' => 'view_own_order']);
        $provider->givePermissionTo('view_own_order');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $store = Store::factory()->create(['user_id' => $provider->id]);
        $customer = User::factory()->create();

        $order1 = Order::create(['user_id' => $customer->id, 'store_id' => $store->id, 'total' => 100]);
        $order1->created_at = Carbon::parse('2024-01-01');
        $order1->save();

        $order2 = Order::create(['user_id' => $customer->id, 'store_id' => $store->id, 'total' => 200]);
        $order2->created_at = Carbon::parse('2024-02-15');
        $order2->save();

        $order3 = Order::create(['user_id' => $customer->id, 'store_id' => $store->id, 'total' => 300]);
        $order3->created_at = Carbon::parse('2024-03-15');
        $order3->save();

        $response = $this->actingAs($provider, 'sanctum')
            ->getJson('/api/v1/orders/my/stats?date_from=2024-02-01&date_to=2024-02-28');

        $response->assertStatus(200)
            ->assertExactJson([
                'total_revenue' => 200,
                'order_count' => 1,
                'avg_order_value' => 200,
            ]);
    }
}

