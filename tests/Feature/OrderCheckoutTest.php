<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_with_provided_addresses(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Permission::create(['name' => 'create_order']);
        $user->givePermissionTo('create_order');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Sanctum::actingAs($user);

        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addToCart([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $payload = [
            'shipping_address' => '123 Rue de Paris',
            'billing_address' => '456 Avenue de Lyon',
        ];

        $response = $this->postJson('/api/v1/orders/checkout', $payload);

        $response->assertCreated()
            ->assertJsonPath('shipping_address', $payload['shipping_address'])
            ->assertJsonPath('billing_address', $payload['billing_address']);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'shipping_address' => $payload['shipping_address'],
            'billing_address' => $payload['billing_address'],
        ]);

        $this->assertDatabaseCount('orders', 1);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals(1, $order->items()->count());
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_uses_cart_id_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Permission::create(['name' => 'create_order']);
        $user->givePermissionTo('create_order');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Sanctum::actingAs($user);

        $cart = Cart::create(['user_id' => $user->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson('/api/v1/orders/checkout');

        $response->assertCreated();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(1, $order->items()->count());

        $this->assertDatabaseCount('cart_items', 0);
    }
}
