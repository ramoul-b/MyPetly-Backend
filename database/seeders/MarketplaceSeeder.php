<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Provider;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = app(\Faker\Generator::class);
        // Créer quelques catégories de produits pour les assigner aux produits
        $categoryData = [
            [
                'name' => ['en' => 'Food', 'fr' => 'Nourriture', 'it' => 'Cibo'],
                'description' => [
                    'en' => 'Food products',
                    'fr' => 'Produits alimentaires',
                    'it' => 'Prodotti alimentari',
                ],
                'icon' => 'restaurant',
                'color' => '#FFA500',
            ],
            [
                'name' => ['en' => 'Accessories', 'fr' => 'Accessoires', 'it' => 'Accessori'],
                'description' => [
                    'en' => 'Pet accessories',
                    'fr' => 'Accessoires pour animaux',
                    'it' => 'Accessori per animali',
                ],
                'icon' => 'shopping-bag',
                'color' => '#795548',
            ],
        ];

        $categories = [];
        foreach ($categoryData as $data) {
            $categories[] = ProductCategory::firstOrCreate([
                'name->en' => $data['name']['en'],
            ], $data);
        }

        // 1. Créer 2 users provider (firstOrCreate évite duplication)
        $providers = [];
        for ($i = 1; $i <= 2; $i++) {
            $providers[] = User::firstOrCreate(
                ['email' => "provider$i@test.com"],
                [
                    'name' => "Provider $i",
                    'password' => Hash::make('password'),
                    'status' => 'active'
                ]
            );
        }

        // 2. Créer 1 store par provider
        $stores = [];
        foreach ($providers as $i => $provider) {
            $stores[] = Store::firstOrCreate(
                ['user_id' => $provider->id],
                [
                    'name' => "Boutique " . ($i + 1),
                    'description' => "La boutique de Provider " . ($i + 1),
                    'status' => 'active'
                ]
            );
        }

        // 3. Créer 6 produits par store
        $products = [];
        foreach ($stores as $store) {
            for ($j = 0; $j < 6; $j++) {
                $products[] = Product::create([
                    'product_category_id' => $categories[array_rand($categories)]->id,
                    'store_id' => $store->id,
                    'name' => [
                        'en' => $faker->unique()->word . ' EN',
                        'fr' => $faker->unique()->word . ' FR',
                        'it' => $faker->unique()->word . ' IT'
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
        $client = User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('password'),
                'status' => 'active'
            ]
        );

        // 5. Créer 1 commande (avec 3 produits)
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
                'order_id'  => $order->id,
                'product_id' => $product->id,
                'quantity'  => $qty,
                'unit_price' => $product->price,
            ]);
            $total += $product->price * $qty;
        }

        $order->update(['total' => $total]);
    }
}
