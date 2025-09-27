<?php

namespace Tests\Unit;

use App\Enums\ProviderStatusEnum;
use App\Models\Provider;
use App\Notifications\ProviderApprovedNotification;
use App\Notifications\ProviderRejectedNotification;
use App\Services\ProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProviderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_status_to_approved_sets_validated_at_and_notifies(): void
    {
        Notification::fake();

        $provider = Provider::factory()->create([
            'status' => ProviderStatusEnum::PENDING,
        ]);

        $service = app(ProviderService::class);
        $service->updateStatus($provider, ProviderStatusEnum::APPROVED);

        $provider->refresh();

        $this->assertEquals(ProviderStatusEnum::APPROVED, $provider->status);
        $this->assertNotNull($provider->validated_at);

        Notification::assertSentTo(
            [$provider->user],
            ProviderApprovedNotification::class
        );
    }

    public function test_update_status_to_rejected_clears_validated_at_and_notifies(): void
    {
        Notification::fake();

        $provider = Provider::factory()->create([
            'status' => ProviderStatusEnum::APPROVED,
            'validated_at' => now(),
        ]);

        $service = app(ProviderService::class);
        $service->updateStatus($provider, ProviderStatusEnum::REJECTED);

        $provider->refresh();

        $this->assertEquals(ProviderStatusEnum::REJECTED, $provider->status);
        $this->assertNull($provider->validated_at);

        Notification::assertSentTo(
            [$provider->user],
            ProviderRejectedNotification::class
        );
    }
}
