<?php

namespace Tests\Unit\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    private CouponService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CouponService();
    }

    public function test_can_manage_coupons_via_service(): void
    {
        $storeOwner = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $storeOwner->id]);
        $product = Product::factory()->create(['store_id' => $store->id]);

        $coupon = $this->service->create([
            'store_id'       => $store->id,
            'product_id'     => $product->id,
            'created_by'     => $storeOwner->id,
            'code'           => 'SERVICE1',
            'name'           => ['fr' => 'Service', 'en' => 'Service'],
            'description'    => ['fr' => 'Desc', 'en' => 'Desc'],
            'discount_type'  => 'fixed',
            'discount_value' => 5,
        ]);

        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertDatabaseHas('coupons', ['code' => 'SERVICE1']);

        $found = $this->service->find($coupon->id);
        $this->assertEquals('SERVICE1', $found->code);

        $list = $this->service->list(['store_id' => $store->id]);
        $this->assertEquals(1, $list->total());

        $updated = $this->service->update($coupon, ['discount_value' => 10]);
        $this->assertEquals(10.0, (float) $updated->discount_value);

        $this->service->delete($updated);
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}
