<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\IVerificacaoEmailService;
use App\Services\VerificacaoEmailService;
use App\Strategies\Emails\VerificacaoEmailStrategy;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private string $token;
    private User $user;
    private IVerificacaoEmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');

        $this->user = User::factory()->create([
            "name" => "Victor",
            "surname" => "Dias",
            "phone_number" => "+55 (11) 91234-5678",
            "email" => "victor.dias@example.com",
            "password" => bcrypt("SenhaForte123!"),
            "terms" => true,
            "email_verified_at" => now(),
            "is_verified" => true
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->emailService = new VerificacaoEmailService(new VerificacaoEmailStrategy());
    }

    public function testUsuarioDeveSerAutenticandoComSucessoQuandoInformarOsDadosCorretos(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'SenhaForte123!',
        ]);

        // Acesse a estrutura correta da resposta
        $responseData = $response->json('data');
        $this->token = $responseData['token'];

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'surname',
                'phone_number',
                'role',
                'token', // Agora está no local correto
                'message'
            ],
        ]);

        $response->assertJsonFragment([
            'id' => $this->user->id,
            'email' => $this->user->email,
        ]);
    }
    public function testUsuarioNaoDeveSerAutenticandoComSucessoQuandoInformadoDadosIncorretos(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => '1234567',
        ]);

        $response->assertStatus(401)->assertJsonStructure(['message']);
    }

    public function testDeveRetornarOsDadosDoUsuarioQuandoInformarUmTokenValido(): void
    {
        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['id', 'name', 'email'],
        ]);

        $response->assertJsonFragment([
            'id' => $this->user->id,
            'email' => $this->user->email,
        ]);
    }

    public function testNaoDeveRetornarOsDadosDoUsuarioQuandoNaoInformadoOToken(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)->assertJsonStructure(['message']);
    }

    public function testNaoDeveRetornarOsDadosDoUsuarioQuandoInformadoOTokenInvalido(): void
    {
        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . 'ivalido',
        ]);

        $response->assertStatus(401)->assertJsonStructure(['message']);
    }

    public function testDeveRetornarErroAoTentarEfetuarLoginComEmailNaoVerificado(): void
    {
        $data = [
            'email' => 'teste@auth.com',
            'password' => '12345678',
        ];

        User::create([...$data, 'name' => 'Teste login', 'password' => bcrypt($data['password'])]);

        $response = $this->postJson('/api/auth/login', $data, []);
        $response->assertForbidden();
    }

    public function testDeveSerPossivelCadastrarUsuario(): void
    {
        $data = [
            'name' => 'Teste autenticação',
            'email' => 'teste@auth.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/auth/register', $data, []);
        $response->assertCreated();
    }

    public function testDeveRetornarErroAoTentarCadastrarUsuarioComEmailCadastrado(): void
    {
        $data = [
            'name' => 'Teste autenticação',
            'email' => $this->user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/auth/register', $data, []);
        $response->assertUnprocessable();
    }

    public function testDeveSerPossivelEnviarEmailDeVerificacao(): void
    {
        $data = [
            'email' => $this->user->email,
        ];

        $response = $this->postJson('/api/auth/verify', $data, []);
        $response->assertOk();
    }
}
