<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_authentication()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role' => 'spectateur',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['token JWT', 'user']);

        $registerResponse = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 'spectateur',
        ]);

        $registerResponse->assertStatus(201)
            ->assertJsonStructure(['message', 'user', 'token JWT']);

        $token = JWTAuth::fromUser($user);
        $logoutResponse = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer $token"
        ]);

        $logoutResponse->assertStatus(200)
            ->assertJson(['message' => 'Déconnexion réussie']);
    }
}
