<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition()
    {
        return [
            'name' => [
                'en' => $this->faker->word,
                'fr' => $this->faker->word,
            ],
            'description' => $this->faker->optional()->passthrough([
                'en' => $this->faker->sentence,
                'fr' => $this->faker->sentence,
            ]),
        ];
    }
}
