<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'store_id'            => Store::factory(),
            'product_id'          => Product::factory(),
            'created_by'          => User::factory(),
            'code'                => strtoupper(Str::random(8)),
            'name'                => ['fr' => $this->faker->words(2, true), 'en' => $this->faker->words(2, true)],
            'description'         => ['fr' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'discount_type'       => $this->faker->randomElement(['percentage', 'fixed']),
            'discount_value'      => $this->faker->randomFloat(2, 5, 20),
            'minimum_order_total' => $this->faker->randomFloat(2, 0, 200),
            'usage_limit'         => $this->faker->numberBetween(10, 200),
            'used_count'          => 0,
            'starts_at'           => now()->subDays(5),
            'expires_at'          => now()->addMonth(),
            'is_active'           => true,
        ];
    }
}
