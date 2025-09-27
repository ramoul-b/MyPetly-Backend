<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rotates_existing_tokens_and_returns_expiration(): void
    {
        config(['sanctum.expiration' => 45]);

        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $staleToken = $user->createToken('stale')->plainTextToken;

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_at',
                'expires_in',
                'user' => [
                    'id',
                    'roles',
                    'permissions',
                ],
            ]);

        $data = $response->json();

        $this->assertNotEquals($staleToken, $data['access_token']);
        $this->assertSame('Bearer', $data['token_type']);
        $this->assertGreaterThan(0, $data['expires_in']);

        $tokens = PersonalAccessToken::where('tokenable_id', $user->id)->get();
        $this->assertCount(1, $tokens);

        $storedToken = $tokens->first();
        $this->assertNotNull($storedToken->expires_at);

        $this->assertSame(
            $storedToken->expires_at->toIso8601String(),
            Carbon::parse($data['expires_at'])->toIso8601String()
        );
    }
}
