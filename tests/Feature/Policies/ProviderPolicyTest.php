<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Models\Provider;
use App\Policies\ProviderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProviderPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create permissions used in the policy
        Permission::create(['name' => 'view-providers']);
        Permission::create(['name' => 'approve-providers']);
    }

    public function test_admin_permissions()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(['view-providers', 'approve-providers']);
        $provider = Provider::factory()->create();

        $policy = new ProviderPolicy();
        $this->assertTrue($policy->view($admin, $provider));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $provider));
        $this->assertTrue($policy->delete($admin, $provider));
    }

    public function test_owner_permissions()
    {
        $owner = User::factory()->create();
        $provider = Provider::factory()->create(['user_id' => $owner->id]);
        $anotherProvider = Provider::factory()->create(['user_id' => User::factory()->create()->id]); // autre owner

        $policy = new ProviderPolicy();
        $this->assertTrue($policy->view($owner, $provider));
        $this->assertFalse($policy->view($owner, $anotherProvider));
        $this->assertFalse($policy->create($owner));
        $this->assertTrue($policy->update($owner, $provider));
        $this->assertFalse($policy->update($owner, $anotherProvider));
        $this->assertTrue($policy->delete($owner, $provider));
        $this->assertFalse($policy->delete($owner, $anotherProvider));
    }

    public function test_guest_permissions()
    {
        $guest = User::factory()->create();
        $provider = Provider::factory()->create();

        $policy = new ProviderPolicy();
        $this->assertFalse($policy->view($guest, $provider));
        $this->assertFalse($policy->create($guest));
        $this->assertFalse($policy->update($guest, $provider));
        $this->assertFalse($policy->delete($guest, $provider));
    }
}
