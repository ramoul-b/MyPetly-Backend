<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProviderPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_photo_upload_successful(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $provider = Provider::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('access')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/providers/' . $provider->id . '/photo', [
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(200)->assertJsonStructure(['message', 'photo_url']);

        $provider->refresh();
        $this->assertNotNull($provider->photo);
        Storage::disk('public')->assertExists($provider->photo);
    }

    public function test_provider_photo_upload_validation_error(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $provider = Provider::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('access')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/providers/' . $provider->id . '/photo', [
            'photo' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
        ]);

        $response->assertStatus(422);
        Storage::disk('public')->assertMissing('providers/doc.pdf');
    }
}
