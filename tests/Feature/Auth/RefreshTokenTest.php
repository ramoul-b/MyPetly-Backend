<?php

namespace Tests\Feature;

// tests/Feature/Auth/RefreshTokenTest.php

use Tests\TestCase;

class ExampleTest extends TestCase
{
    
    public function test_refresh_token_works()
    {
        $user  = User::factory()->create();

        $token = $user->createToken('access')->plainTextToken;

        $response = $this->withToken($token)
                        ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
                ->assertJsonStructure(['access_token', 'user' => ['roles'], 'permissions']);
    }
}