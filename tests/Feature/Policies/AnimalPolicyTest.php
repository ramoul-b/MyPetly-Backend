<?php

namespace Tests\Feature\Policies;

use App\Models\Animal;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Policies\AnimalPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnimalPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'provider']);
    }

    public function test_provider_without_bookings_cannot_view_animals()
    {
        $providerUser = User::factory()->create();
        $providerUser->assignRole('provider');
        Provider::factory()->create(['user_id' => $providerUser->id]);

        $owner = User::factory()->create();
        $animal = Animal::create([
            'name' => 'Buddy',
            'user_id' => $owner->id,
        ]);

        $policy = new AnimalPolicy;
        $this->assertFalse($policy->view($providerUser, $animal));
        $this->assertFalse($policy->viewAny($providerUser));
    }

    public function test_provider_with_bookings_can_view_animals()
    {
        $providerUser = User::factory()->create();
        $providerUser->assignRole('provider');
        $provider = Provider::factory()->create(['user_id' => $providerUser->id]);

        $category = Category::create([
            'name' => ['en' => 'cat', 'fr' => 'cat'],
            'icon' => 'icon',
            'type' => 'type',
            'color' => '#fff',
        ]);
        $service = Service::create([
            'name' => ['en' => 's', 'fr' => 's'],
            'description' => ['en' => 'd', 'fr' => 'd'],
            'price' => 10,
            'active' => true,
            'icon' => 'i',
            'color' => '#fff',
            'category_id' => $category->id,
        ]);

        $owner = User::factory()->create();
        $animal = Animal::create([
            'name' => 'Buddy',
            'user_id' => $owner->id,
        ]);

        Booking::create([
            'service_id' => $service->id,
            'provider_id' => $provider->id,
            'user_id' => $owner->id,
            'animal_id' => $animal->id,
            'appointment_date' => now(),
            'time' => '10:00',
            'payment_intent' => 'pi',
            'currency' => 'eur',
            'status' => 'pending',
        ]);

        $policy = new AnimalPolicy;
        $this->assertTrue($policy->view($providerUser, $animal));
        $this->assertTrue($policy->viewAny($providerUser));
    }
}
