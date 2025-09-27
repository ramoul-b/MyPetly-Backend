<?php

namespace Tests\Feature\Marketplace\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StoreSettingApiTest extends TestCase
{
    use RefreshDatabase;

    private array $permissions = [
        'view_any_store_setting',
        'create_store_setting',
        'edit_any_store_setting',
        'delete_any_store_setting',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    public function test_store_setting_crud_endpoints(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo($this->permissions);
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);

        $payload = [
            'store_id'              => $store->id,
            'currency'              => 'EUR',
            'timezone'              => 'Europe/Paris',
            'locale'                => 'fr',
            'inventory_tracking'    => true,
            'notifications_enabled' => true,
            'low_stock_threshold'   => 5,
        ];

        $createResponse = $this->postJson('/api/v1/store-settings', $payload);
        $createResponse->assertStatus(201)->assertJsonPath('store_id', $store->id);
        $settingId = $createResponse->json('id');

        $this->getJson('/api/v1/store-settings')
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $settingId]);

        $this->getJson("/api/v1/store-settings/{$settingId}")
            ->assertStatus(200)
            ->assertJsonPath('id', $settingId);

        $updatePayload = [
            'currency'            => 'USD',
            'low_stock_threshold' => 3,
        ];

        $this->putJson("/api/v1/store-settings/{$settingId}", $updatePayload)
            ->assertStatus(200)
            ->assertJsonPath('currency', 'USD')
            ->assertJsonPath('low_stock_threshold', 3);

        $this->deleteJson("/api/v1/store-settings/{$settingId}")
            ->assertStatus(200)
            ->assertJsonFragment(['message' => 'Store setting deleted']);

        $this->assertDatabaseMissing('store_settings', ['id' => $settingId]);
    }
}
