<?php

namespace Tests\Feature\Auth;

// tests/Feature/Auth/RefreshTokenTest.php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_token_works()
    {
        $user  = User::factory()->create();

        $token = $user->createToken('access')->plainTextToken;

        $response = $this->withToken($token)
                        ->postJson('/api/v1/refresh-token');

        $response->assertStatus(200)
                ->assertJsonStructure(['access_token', 'user' => ['roles'], 'permissions']);
    }
}