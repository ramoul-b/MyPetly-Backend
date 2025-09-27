<?php

namespace Tests\Unit\Services;

use App\Models\Store;
use App\Models\StoreSetting;
use App\Services\StoreSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    private StoreSettingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StoreSettingService();
    }

    public function test_manage_store_settings(): void
    {
        $store = Store::factory()->create();

        $setting = $this->service->create([
            'store_id'              => $store->id,
            'currency'              => 'EUR',
            'timezone'              => 'Europe/Paris',
            'locale'                => 'fr',
            'inventory_tracking'    => true,
            'notifications_enabled' => true,
            'low_stock_threshold'   => 5,
        ]);

        $this->assertInstanceOf(StoreSetting::class, $setting);

        $found = $this->service->getByStore($store->id);
        $this->assertEquals($setting->id, $found->id);

        $updated = $this->service->update($setting, ['currency' => 'USD']);
        $this->assertEquals('USD', $updated->currency);

        $this->service->delete($updated);
        $this->assertDatabaseMissing('store_settings', ['id' => $setting->id]);
    }
}
