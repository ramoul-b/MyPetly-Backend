<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'product_category_id' => ProductCategory::factory(),
            'store_id'            => Store::factory(),
            'name'                => [
                'en' => $this->faker->word,
                'fr' => $this->faker->word,
            ],
            'description'         => [
                'en' => $this->faker->sentence,
                'fr' => $this->faker->sentence,
            ],
            'price'               => $this->faker->randomFloat(2, 1, 100),
            'stock'               => $this->faker->numberBetween(0, 100),
            'image'               => $this->faker->imageUrl(),
        ];
    }
}

