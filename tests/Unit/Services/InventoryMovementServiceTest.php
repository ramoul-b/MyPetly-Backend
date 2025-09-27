<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\InventoryMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryMovementServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryMovementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InventoryMovementService();
    }

    public function test_inventory_service_updates_stock_correctly(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'store_id' => $store->id,
            'stock'    => 0,
        ]);

        $movement = $this->service->create([
            'store_id'      => $store->id,
            'product_id'    => $product->id,
            'user_id'       => $user->id,
            'movement_type' => 'in',
            'quantity'      => 5,
        ]);

        $product->refresh();
        $this->assertSame(5, $product->stock);

        $movement = $this->service->update($movement, [
            'movement_type' => 'out',
            'quantity'      => 3,
        ]);

        $product->refresh();
        $this->assertSame(-3, $product->stock);

        $this->service->delete($movement);

        $product->refresh();
        $this->assertSame(0, $product->stock);
    }
}
