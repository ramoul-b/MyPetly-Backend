<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authentication_is_required(): void
    {
        $this->getJson('/api/v1/admin/dashboard/stats')->assertStatus(401);
    }

    public function test_permission_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/dashboard/stats')
            ->assertStatus(403);
    }

    public function test_it_returns_aggregated_stats_with_date_filters(): void
    {
        $admin = User::factory()->create();
        Permission::create(['name' => 'view_admin_dashboard']);
        $admin->givePermissionTo('view_admin_dashboard');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $store = Store::factory()->create();
        $customer = User::factory()->create();

        $orderInRangeCompleted = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'total' => 120,
            'status' => 'completed',
        ]);
        Order::whereKey($orderInRangeCompleted->id)->update([
            'created_at' => Carbon::parse('2024-02-10 10:00:00'),
            'updated_at' => Carbon::parse('2024-02-10 10:00:00'),
        ]);

        $orderInRangePending = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'total' => 80,
            'status' => 'pending',
        ]);
        Order::whereKey($orderInRangePending->id)->update([
            'created_at' => Carbon::parse('2024-02-15 12:00:00'),
            'updated_at' => Carbon::parse('2024-02-15 12:00:00'),
        ]);

        $orderOutOfRange = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'total' => 200,
            'status' => 'completed',
        ]);
        Order::whereKey($orderOutOfRange->id)->update([
            'created_at' => Carbon::parse('2023-12-25 08:00:00'),
            'updated_at' => Carbon::parse('2023-12-25 08:00:00'),
        ]);

        $pendingProviderUser = User::factory()->create(['status' => 'pending']);
        $activeProviderUser = User::factory()->create();
        User::factory()->create(['status' => 'pending']);

        $pendingProvider = Provider::create([
            'user_id' => $pendingProviderUser->id,
            'name' => ['en' => 'Pending Provider'],
            'email' => 'pending-provider@example.com',
        ]);

        $activeProvider = Provider::create([
            'user_id' => $activeProviderUser->id,
            'name' => ['en' => 'Active Provider'],
            'email' => 'active-provider@example.com',
        ]);

        $category = Category::create([
            'name' => ['en' => 'Care'],
            'icon' => 'fa-paw',
            'type' => 'service',
            'color' => '#FF0000',
        ]);

        $service = Service::create([
            'name' => ['en' => 'Grooming'],
            'description' => ['en' => 'Full grooming'],
            'category_id' => $category->id,
            'price' => 45,
            'active' => true,
        ]);

        $bookingInRangePending = Booking::create([
            'service_id' => $service->id,
            'provider_id' => $activeProvider->id,
            'user_id' => $customer->id,
            'animal_id' => null,
            'appointment_date' => Carbon::parse('2024-03-01 09:00:00'),
            'time' => '09:00',
            'payment_intent' => 'pi_123',
            'currency' => 'eur',
            'status' => 'pending',
        ]);
        Booking::whereKey($bookingInRangePending->id)->update([
            'created_at' => Carbon::parse('2024-02-20 09:00:00'),
            'updated_at' => Carbon::parse('2024-02-20 09:00:00'),
        ]);

        $bookingInRangeConfirmed = Booking::create([
            'service_id' => $service->id,
            'provider_id' => $activeProvider->id,
            'user_id' => $customer->id,
            'animal_id' => null,
            'appointment_date' => Carbon::parse('2024-03-05 11:00:00'),
            'time' => '11:00',
            'payment_intent' => 'pi_456',
            'currency' => 'eur',
            'status' => 'confirmed',
        ]);
        Booking::whereKey($bookingInRangeConfirmed->id)->update([
            'created_at' => Carbon::parse('2024-02-22 11:00:00'),
            'updated_at' => Carbon::parse('2024-02-22 11:00:00'),
        ]);

        $bookingOutOfRange = Booking::create([
            'service_id' => $service->id,
            'provider_id' => $activeProvider->id,
            'user_id' => $customer->id,
            'animal_id' => null,
            'appointment_date' => Carbon::parse('2023-11-10 14:00:00'),
            'time' => '14:00',
            'payment_intent' => 'pi_789',
            'currency' => 'eur',
            'status' => 'pending',
        ]);
        Booking::whereKey($bookingOutOfRange->id)->update([
            'created_at' => Carbon::parse('2023-11-10 14:00:00'),
            'updated_at' => Carbon::parse('2023-11-10 14:00:00'),
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/dashboard/stats?date_from=2024-02-01&date_to=2024-03-01');

        $response->assertStatus(200);

        $response->assertJson([
            'total_revenue' => 200.0,
            'orders_volume' => 2,
            'average_order_value' => 100.0,
            'pending_orders' => 1,
            'completed_orders' => 1,
            'bookings_volume' => 2,
            'pending_bookings' => 1,
            'pending_users' => 2,
            'pending_providers' => 1,
            'total_users' => 6,
            'total_providers' => 2,
        ]);
    }
}
