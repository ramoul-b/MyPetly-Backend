<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CartService();
    }

    public function test_add_and_update_cart_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $this->actingAs($user);

        $this->service->addToCart(['product_id' => $product->id, 'quantity' => 1]);
        $cartId = $this->service->getUserCart()->id;
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cartId,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->service->addToCart(['product_id' => $product->id, 'quantity' => 3]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cartId,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_remove_and_clear_cart(): void
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(2)->create();
        $this->actingAs($user);

        $items = [];
        foreach ($products as $product) {
            $items[] = $this->service->addToCart(['product_id' => $product->id, 'quantity' => 1]);
        }
        $this->assertDatabaseCount('cart_items', 2);

        $this->service->remove($items[0]);
        $this->assertDatabaseMissing('cart_items', ['id' => $items[0]->id]);
        $this->assertDatabaseCount('cart_items', 1);

        $this->service->clear();
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_creates_order_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $storeProduct = Product::factory()->count(2)->create();
        $this->actingAs($user);

        foreach ($storeProduct as $product) {
            $this->service->addToCart(['product_id' => $product->id, 'quantity' => 2]);
        }

        $order = $this->service->checkout('addr1', 'addr2');

        $this->assertEquals('addr1', $order->shipping_address);
        $this->assertEquals('addr2', $order->billing_address);

        $this->assertDatabaseCount('cart_items', 0);
        $this->assertEquals(1, Order::count());
        $this->assertEquals(2, OrderItem::count());
        $this->assertEquals($order->id, OrderItem::first()->order_id);
    }
}
