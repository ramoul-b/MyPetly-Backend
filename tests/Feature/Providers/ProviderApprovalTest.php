<?php

namespace Tests\Feature\Providers;

use App\Enums\ProviderStatusEnum;
use App\Models\Provider;
use App\Models\User;
use App\Notifications\ProviderApprovedNotification;
use App\Notifications\ProviderRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class ProviderApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'view-providers']);
        Permission::create(['name' => 'approve-providers']);
    }

    public function test_admin_can_approve_provider(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->givePermissionTo(['approve-providers', 'view-providers']);
        $provider = Provider::factory()->create();
        $token = $admin->createToken('access')->plainTextToken;

        $response = $this->withToken($token)->patchJson("/api/v1/providers/{$provider->id}/status", [
            'status' => ProviderStatusEnum::APPROVED->value,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', ProviderStatusEnum::APPROVED->value);

        $provider->refresh();
        $this->assertEquals(ProviderStatusEnum::APPROVED, $provider->status);
        $this->assertNotNull($provider->validated_at);

        Notification::assertSentTo(
            [$provider->user],
            ProviderApprovedNotification::class
        );
    }

    public function test_admin_can_reject_provider(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->givePermissionTo(['approve-providers', 'view-providers']);
        $provider = Provider::factory()->create([
            'status' => ProviderStatusEnum::PENDING,
            'validated_at' => now(),
        ]);
        $token = $admin->createToken('access')->plainTextToken;

        $response = $this->withToken($token)->patchJson("/api/v1/providers/{$provider->id}/status", [
            'status' => ProviderStatusEnum::REJECTED->value,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', ProviderStatusEnum::REJECTED->value);

        $provider->refresh();
        $this->assertEquals(ProviderStatusEnum::REJECTED, $provider->status);
        $this->assertNull($provider->validated_at);

        Notification::assertSentTo(
            [$provider->user],
            ProviderRejectedNotification::class
        );
    }

    public function test_user_without_permission_cannot_update_status(): void
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create();
        $token = $user->createToken('access')->plainTextToken;

        $response = $this->withToken($token)->patchJson("/api/v1/providers/{$provider->id}/status", [
            'status' => ProviderStatusEnum::APPROVED->value,
        ]);

        $response->assertStatus(403);
    }

    public function test_validation_error_when_status_is_invalid(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->givePermissionTo(['approve-providers', 'view-providers']);
        $provider = Provider::factory()->create();
        $token = $admin->createToken('access')->plainTextToken;

        $response = $this->withToken($token)->patchJson("/api/v1/providers/{$provider->id}/status", [
            'status' => 'unknown',
        ]);

        $response->assertStatus(422);

        Notification::assertNothingSent();
    }
}
