<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition()
    {
        return [
            'user_id'   => User::factory(), // Relation avec l'utilisateur owner
            'name'      => $this->faker->company,
            'email'     => $this->faker->unique()->companyEmail,
            'phone'     => $this->faker->phoneNumber,
            'address'   => $this->faker->address,
            // ajoute d’autres champs selon ta table, exemple :
            // 'description' => $this->faker->sentence,
        ];
    }
}
