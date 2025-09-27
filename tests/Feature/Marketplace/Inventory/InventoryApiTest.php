<?php

namespace Tests\Feature\Marketplace\Inventory;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    private array $permissions = [
        'view_any_inventory_movement',
        'create_inventory_movement',
        'edit_any_inventory_movement',
        'delete_any_inventory_movement',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    public function test_inventory_movement_crud_flow(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo($this->permissions);
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'store_id' => $store->id,
            'stock'    => 10,
        ]);

        $createPayload = [
            'store_id'      => $store->id,
            'product_id'    => $product->id,
            'movement_type' => 'in',
            'quantity'      => 5,
            'reference'     => 'REF-1',
        ];

        $createResponse = $this->postJson('/api/v1/inventory-movements', $createPayload);
        $createResponse->assertStatus(201)->assertJsonPath('quantity', 5);
        $movementId = $createResponse->json('id');

        $product->refresh();
        $this->assertSame(15, $product->stock);

        $this->getJson('/api/v1/inventory-movements')
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $movementId]);

        $this->getJson("/api/v1/inventory-movements/{$movementId}")
            ->assertStatus(200)
            ->assertJsonPath('id', $movementId);

        $updatePayload = [
            'movement_type' => 'out',
            'quantity'      => 2,
            'notes'         => 'Correction',
        ];

        $this->putJson("/api/v1/inventory-movements/{$movementId}", $updatePayload)
            ->assertStatus(200)
            ->assertJsonPath('movement_type', 'out')
            ->assertJsonPath('quantity', 2);

        $product->refresh();
        $this->assertSame(8, $product->stock);

        $this->deleteJson("/api/v1/inventory-movements/{$movementId}")
            ->assertStatus(200)
            ->assertJsonFragment(['message' => 'Inventory movement deleted']);

        $product->refresh();
        $this->assertSame(10, $product->stock);

        $this->assertDatabaseMissing('inventory_movements', ['id' => $movementId]);
    }
}
