<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function test_assign_roles_and_revoke_role()
    {
        $user = User::factory()->create();
        $roleA = Role::create(['name' => 'role-a']);
        $roleB = Role::create(['name' => 'role-b']);

        $this->service->assignRoles($user->id, [$roleA->name, $roleB->name]);
        $this->assertCount(2, $this->service->getRoles($user->id));

        $this->service->revokeRole($user->id, $roleA->id);
        $this->assertCount(1, $this->service->getRoles($user->id));
    }

    public function test_get_permissions_and_search_and_delete()
    {
        $user = User::factory()->create([ 'name' => 'John Doe', 'email' => 'jdoe@example.com']);
        Permission::create(['name' => 'view_any_provider']);
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo('view_any_provider');
        $user->assignRole('admin');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = $this->service->getPermissions($user->id);
        $this->assertIsIterable($permissions);

        $results = $this->service->searchUsers('jdoe');
        $this->assertTrue($results->contains('id', $user->id));

        $this->service->deleteUser($user->id);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
