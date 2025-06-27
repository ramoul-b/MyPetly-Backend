<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Models\Product;
use App\Models\Store;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'view_any_product']);
        Permission::create(['name' => 'view_own_product']);
        Permission::create(['name' => 'create_product']);
        Permission::create(['name' => 'edit_any_product']);
        Permission::create(['name' => 'edit_own_product']);
        Permission::create(['name' => 'delete_any_product']);
        Permission::create(['name' => 'delete_own_product']);
    }

    public function test_admin_permissions()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(['view_any_product', 'create_product', 'edit_any_product', 'delete_any_product']);
        $store = Store::factory()->create(['user_id' => $admin->id]);
        $product = Product::factory()->create(['store_id' => $store->id]);

        $policy = new ProductPolicy();
        $this->assertTrue($policy->view($admin, $product));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $product));
        $this->assertTrue($policy->delete($admin, $product));
    }

    public function test_owner_permissions()
    {
        $owner = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);
        $product = Product::factory()->create(['store_id' => $store->id]);
        $otherProduct = Product::factory()->create();
        $owner->givePermissionTo(['view_own_product', 'edit_own_product', 'delete_own_product']);

        $policy = new ProductPolicy();
        $this->assertTrue($policy->view($owner, $product));
        $this->assertFalse($policy->view($owner, $otherProduct));
        $this->assertFalse($policy->create($owner));
        $this->assertTrue($policy->update($owner, $product));
        $this->assertFalse($policy->update($owner, $otherProduct));
        $this->assertTrue($policy->delete($owner, $product));
        $this->assertFalse($policy->delete($owner, $otherProduct));
    }

    public function test_guest_permissions()
    {
        $guest = User::factory()->create();
        $product = Product::factory()->create();

        $policy = new ProductPolicy();
        $this->assertFalse($policy->view($guest, $product));
        $this->assertFalse($policy->create($guest));
        $this->assertFalse($policy->update($guest, $product));
        $this->assertFalse($policy->delete($guest, $product));
    }
}
