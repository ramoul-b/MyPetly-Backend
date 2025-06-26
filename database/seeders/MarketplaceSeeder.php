<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Provider;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Créer 2 users provider et leurs providers associés
        $providers = [];
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'name' => "Provider $i",
                'email' => "provider$i@test.com",
                'password' => Hash::make('password'),
                'status' => 'active'
            ]);

            $providers[] = Provider::factory()->create(['user_id' => $user->id]);
        }

        // 2. Créer 1 store par provider
        $stores = [];
        foreach ($providers as $i => $provider) {
            $stores[] = Store::create([
                'name' => "Boutique " . ($i + 1),
                'description' => "La boutique de Provider " . ($i + 1),
                'provider_id' => $provider->id,
            ]);
        }

        // 3. Créer 6 produits par store
        $products = [];
        foreach ($stores as $store) {
            for ($j = 0; $j < 6; $j++) {
                $products[] = Product::create([
                    'store_id' => $store->id,
                    'name' => [
                        'en' => $faker->word . ' EN',
                        'fr' => $faker->word . ' FR',
                        'it' => $faker->word . ' IT'
                    ],
                    'description' => [
                        'en' => $faker->sentence,
                        'fr' => $faker->sentence,
                        'it' => $faker->sentence
                    ],
                    'price' => $faker->randomFloat(2, 10, 100),
                    'stock' => rand(5, 50),
                    'status' => 'active'
                ]);
            }
        }

        // 4. Créer 1 user client
        $client = User::create([
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'password' => Hash::make('password'),
            'status' => 'active'
        ]);

        // 5. Créer 1 commande (avec 3 produits, sur 2 stores)
        $order = Order::create([
            'user_id' => $client->id,
            'store_id' => $stores[0]->id,
            'total' => 0,
            'status' => 'pending',
        ]);

        $selected = [$products[0], $products[1], $products[6]];
        $total = 0;

        foreach ($selected as $product) {
            $qty = rand(1, 2);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'price' => $product->price
            ]);
            $total += $product->price * $qty;
        }

        $order->update(['total' => $total]);
    }
}
