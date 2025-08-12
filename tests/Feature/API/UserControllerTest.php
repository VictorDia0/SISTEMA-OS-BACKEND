<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    private string $token;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');

        $this->user = User::factory()->create();
        $this->token = Auth::login($this->user);
    }

    public function testLoginSuccess()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

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
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'email', 'token']
        ]);

        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
            'device' => 'testing-device',
        ]);
    }

    public function testLoginFailsWithInvalidCredentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Credenciais inválidas.',
        ]);
    }

    public function testGetUserByIdSuccess()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ]);
    }

    public function testGetUserByIdNotFound()
    {
        $response = $this->getJson("/api/users/non-existent-id");

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Usuário não encontrado.',
        ]);
    }

    public function testUpdateUserDataSuccess()
    {
        $user = User::factory()->create();
        $payload = [
            'name' => 'Novo Nome',
            'email' => 'novoemail@example.com',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Dados atualizados com sucesso.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Novo Nome',
            'email' => 'novoemail@example.com',
        ]);
    }

    public function testMarkEmailAsVerified()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->postJson("/api/users/{$user->id}/verify-email");

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
