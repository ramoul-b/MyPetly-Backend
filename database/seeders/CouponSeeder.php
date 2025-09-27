<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->first() ?? Store::factory()->create();
        $product = Product::query()->where('store_id', $store->id)->first() ?? Product::factory()->create([
            'store_id' => $store->id,
        ]);
        $creator = User::query()->first() ?? User::factory()->create();

        Coupon::query()->firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'store_id'        => $store->id,
                'product_id'      => $product->id,
                'created_by'      => $creator->id,
                'name'            => ['fr' => 'Réduction de bienvenue', 'en' => 'Welcome discount'],
                'description'     => ['fr' => '10% sur votre première commande', 'en' => '10% off your first order'],
                'discount_type'   => 'percentage',
                'discount_value'  => 10,
                'minimum_order_total' => 50,
                'usage_limit'     => 100,
                'used_count'      => 0,
                'starts_at'       => now()->startOfDay(),
                'expires_at'      => now()->addMonths(3),
                'is_active'       => true,
            ]
        );

        Coupon::factory()->count(2)->create([
            'store_id'   => $store->id,
            'product_id' => $product->id,
            'created_by' => $creator->id,
        ]);
    }
}
