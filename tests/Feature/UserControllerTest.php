<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginSuccess()
    {
        // Cria um usuário no banco para teste
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Faz a requisição POST para login
        $response = $this->postJson(
            '/api/login',
            [
                'email' => $user->email,
                'password' => 'password123',
            ],
            [
                'User-Agent' => 'testing-device',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data' => ['id', 'name', 'email', 'token']]);

        // Verifica se o refresh token foi criado no banco
        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
            'device' => 'testing-device',
        ]);
    }

    public function testRefreshSuccess()
    {
        $user = User::factory()->create();

        // Cria manualmente um refresh token para o usuário
        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', 'valid_refresh_token'),
            'device' => 'testing-device',
            'expires_at' => now()->addMinutes(60),
        ]);

        // Faz a requisição POST para refresh enviando o cookie de refresh token
        $response = $this->withCookie('refresh_token', 'valid_refresh_token')
            ->withHeaders(['User-Agent' => 'testing-device'])
            ->postJson('/api/refresh');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data' => ['token']]);
    }

    public function testLogoutSuccess()
    {
        $user = User::factory()->create();

        // Autentica o usuário via JWT para proteger rota logout
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        // Cria um refresh token para o usuário
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', 'token_to_delete'),
            'device' => 'testing-device',
            'expires_at' => now()->addMinutes(60),
        ]);

        // Requisição POST para logout autenticada com token JWT
        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Logout realizado com sucesso.',
        ]);

        // Verifica se os refresh tokens do usuário foram deletados
        $this->assertDatabaseMissing('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }
}
