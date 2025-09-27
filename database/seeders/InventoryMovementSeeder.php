<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

class InventoryMovementSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->first() ?? Store::factory()->create();
        $product = Product::query()->where('store_id', $store->id)->first() ?? Product::factory()->create([
            'store_id' => $store->id,
        ]);
        $user = User::query()->first() ?? User::factory()->create();

        InventoryMovement::factory()->count(5)->create([
            'store_id'   => $store->id,
            'product_id' => $product->id,
            'user_id'    => $user->id,
        ]);
    }
}
