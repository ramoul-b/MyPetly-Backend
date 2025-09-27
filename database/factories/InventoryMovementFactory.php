<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        return [
            'store_id'      => Store::factory(),
            'product_id'    => Product::factory(),
            'user_id'       => User::factory(),
            'movement_type' => $this->faker->randomElement(['in', 'out']),
            'quantity'      => $this->faker->numberBetween(1, 50),
            'reference'     => $this->faker->uuid(),
            'notes'         => $this->faker->sentence(),
            'occurred_at'   => now()->subDays(rand(0, 10)),
        ];
    }
}
