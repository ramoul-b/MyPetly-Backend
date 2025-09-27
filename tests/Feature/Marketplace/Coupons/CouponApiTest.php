<?php

namespace Tests\Feature\Marketplace\Coupons;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CouponApiTest extends TestCase
{
    use RefreshDatabase;

    private array $permissions = [
        'view_any_coupon',
        'create_coupon',
        'edit_any_coupon',
        'delete_any_coupon',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    public function test_coupon_crud_endpoints(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo($this->permissions);
        Sanctum::actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['store_id' => $store->id]);

        $createPayload = [
            'store_id'       => $store->id,
            'product_id'     => $product->id,
            'code'           => 'WELCOME10',
            'name'           => ['fr' => 'Bienvenue', 'en' => 'Welcome'],
            'description'    => ['fr' => 'Réduction', 'en' => 'Discount'],
            'discount_type'  => 'percentage',
            'discount_value' => 10,
        ];

        $createResponse = $this->postJson('/api/v1/coupons', $createPayload);
        $createResponse->assertStatus(201)->assertJsonPath('code', 'WELCOME10');
        $couponId = $createResponse->json('id');

        $this->getJson('/api/v1/coupons')
            ->assertStatus(200)
            ->assertJsonFragment(['code' => 'WELCOME10']);

        $this->getJson("/api/v1/coupons/{$couponId}")
            ->assertStatus(200)
            ->assertJsonPath('id', $couponId);

        $updatePayload = [
            'discount_value' => 15,
            'is_active'      => false,
        ];

        $this->putJson("/api/v1/coupons/{$couponId}", $updatePayload)
            ->assertStatus(200)
            ->assertJsonPath('discount_value', 15.0)
            ->assertJsonPath('is_active', false);

        $this->deleteJson("/api/v1/coupons/{$couponId}")
            ->assertStatus(200)
            ->assertJsonFragment(['message' => 'Coupon deleted']);

        $this->assertDatabaseMissing('coupons', ['id' => $couponId]);
    }
}
