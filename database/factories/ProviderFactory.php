<?php

namespace Database\Factories;

use App\Enums\ProviderStatusEnum;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition()
    {
        return [
            'user_id'      => User::factory(),
            'name'         => [
                'fr' => $this->faker->company,
                'en' => $this->faker->company,
            ],
            'email'        => $this->faker->unique()->companyEmail,
            'phone'        => $this->faker->phoneNumber,
            'tax_code'     => $this->faker->bothify('??######'),
            'address'      => $this->faker->address,
            'status'       => ProviderStatusEnum::PENDING,
            'validated_at' => null,
        ];
    }
}
