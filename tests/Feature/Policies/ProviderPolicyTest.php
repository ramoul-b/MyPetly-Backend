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
        // Crée les permissions nécessaires
        Permission::create(['name' => 'view_any_provider']);
        Permission::create(['name' => 'edit_any_provider']);
        Permission::create(['name' => 'delete_any_provider']);
        Permission::create(['name' => 'create_provider']);
        Permission::create(['name' => 'view_own_provider']);
        Permission::create(['name' => 'edit_own_provider']);
        Permission::create(['name' => 'delete_own_provider']);
    }

    public function test_admin_permissions()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(['view_any_provider', 'edit_any_provider', 'delete_any_provider', 'create_provider']);
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
        $owner->givePermissionTo(['view_own_provider', 'edit_own_provider', 'delete_own_provider', 'create_provider']);
        $provider = Provider::factory()->create(['user_id' => $owner->id]);
        $anotherProvider = Provider::factory()->create(['user_id' => 999]); // autre owner

        $policy = new ProviderPolicy();
        $this->assertTrue($policy->view($owner, $provider));
        $this->assertFalse($policy->view($owner, $anotherProvider));
        $this->assertTrue($policy->create($owner));
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
