<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\StoreSetting;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        Store::query()->each(function (Store $store) {
            StoreSetting::query()->firstOrCreate(
                ['store_id' => $store->id],
                [
                    'currency'               => 'EUR',
                    'timezone'               => 'Europe/Paris',
                    'locale'                 => 'fr',
                    'inventory_tracking'     => true,
                    'notifications_enabled'  => true,
                    'low_stock_threshold'    => 10,
                    'metadata'               => [
                        'delivery' => ['enabled' => true],
                    ],
                ]
            );
        });
    }
}
