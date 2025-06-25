<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition()
    {
        return [
            'provider_id' => Provider::factory(),
            'name'        => [
                'en' => $this->faker->company,
                'fr' => $this->faker->company,
            ],
            'description' => [
                'en' => $this->faker->sentence,
                'fr' => $this->faker->sentence,
            ],
            'address'     => $this->faker->address,
            'phone'       => $this->faker->phoneNumber,
            'email'       => $this->faker->unique()->companyEmail,
        ];
    }
}
