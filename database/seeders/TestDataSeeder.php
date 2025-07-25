<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Provider;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create product categories
        $categoryData = [
            [
                'name' => ['en' => 'Food', 'fr' => 'Nourriture'],
                'description' => ['en' => 'Food products', 'fr' => 'Produits alimentaires'],
            ],
            [
                'name' => ['en' => 'Accessories', 'fr' => 'Accessoires'],
                'description' => ['en' => 'Pet accessories', 'fr' => 'Accessoires pour animaux'],
            ],
        ];

        $categories = [];
        foreach ($categoryData as $data) {
            $categories[] = ProductCategory::create($data);
        }
        $categoryIds = collect($categories)->pluck('id')->all();

        // Create client user
        $client = User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
        ]);
        $client->assignRole('user');

        $stores = [];

        for ($i = 1; $i <= 2; $i++) {
            // Create provider user
            $providerUser = User::factory()->create([
                'name' => "Provider {$i} User",
                'email' => "provider{$i}@example.com",
            ]);
            $providerUser->assignRole('provider');

            // Create provider
            $provider = Provider::factory()->create([
                'user_id' => $providerUser->id,
                'name' => ['en' => "Provider {$i}", 'fr' => "Fournisseur {$i}"],
                'tax_code' => 'TX' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);

            // Create store
            $store = Store::factory()->create([
                'user_id' => $providerUser->id,
                'name' => ['en' => "Store {$i}", 'fr' => "Magasin {$i}"],
            ]);
            $stores[] = $store;

            // Create 6 products for the store
            for ($j = 1; $j <= 6; $j++) {
                Product::create([
                    'product_category_id' => $categoryIds[($j - 1) % count($categoryIds)],
                    'store_id' => $store->id,
                    'name' => [
                        'en' => "Product {$i}-{$j}",
                        'fr' => "Produit {$i}-{$j}",
                    ],
                    'description' => [
                        'en' => "Description {$i}-{$j}",
                        'fr' => "Description {$i}-{$j}",
                    ],
                    'price' => random_int(5, 100),
                    'stock' => 20,
                    'image' => null,
                    'status' => 'active',
                ]);
            }
        }

        // Create one order for the client at each store with 3 products
        foreach ($stores as $store) {
            $products = $store->products()->take(3)->get();
            $order = Order::create([
                'user_id' => $client->id,
                'store_id' => $store->id,
                'total' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'shipping_address' => '123 Test St',
                'billing_address' => '123 Test St',
            ]);

            $total = 0;
            foreach ($products as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                ]);
                $total += $product->price;
            }

            $order->total = $total;
            $order->save();
        }

        // Add demo cart items for the client
        $productsForCart = $stores[0]->products()->take(2)->get();
        foreach ($productsForCart as $product) {
            CartItem::create([
                'user_id' => $client->id,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }
    }
}

