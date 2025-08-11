<?php

namespace App\Services;

use App\Enums\AccountStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PlanEnum;
use App\Exceptions\AuthException;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements IAuthService
{
    public function __construct(protected IVerificacaoEmailService $emailVerificationService) {}

    public function login(array $credenciais, string $dispositivo): array
    {
        $token = JWTAuth::attempt($credenciais);

        if (!$token) {
            throw new AuthException('Usuario ou senha inválidos', Response::HTTP_UNAUTHORIZED);
        }

        $usuario = Auth::user();

        if (is_null($usuario->email_verified_at)) {
            throw new AuthException(
                'Você precisa verificar seu e-mail antes de fazer login.',
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $refreshToken = RefreshToken::make($usuario, $dispositivo);

            return [
                'access_token' => $token,
                'refresh_token' => $refreshToken['token'],
                'refresh_model' => $refreshToken['model'],
            ];
        } catch (\Exception $e) {
            throw new AuthException(
                'Erro ao gerar o token de renovação. Por favor, tente fazer login novamente mais tarde.'
            );
        }
    }

    public function refresh(string $token, string $dispositivo): array
    {
        try {
            $refreshToken = RefreshToken::validarToken($token, $dispositivo);
            $user = $refreshToken->user;

            $accessToken = JWTAuth::fromUser($user);

            $newTokenPlain = RefreshToken::gerarToken();

            $refreshToken->update([
                'token' => hash('sha256', $newTokenPlain),
                'expires_at' => now()->addMinutes(config('jwt.refresh_ttl')),
                'last_used_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return [
                'access_token' => $accessToken,
                'refresh_token' => $newTokenPlain,
            ];
        } catch (AuthException $th) {
            throw $th;
        } catch (\Exception $e) {
            dd($e);
            throw new AuthException('Erro interno ao renovar token', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(): void
    {
        try {
            RefreshToken::logout(JWTAuth::user(), request()->header('User-Agent'));
            JWTAuth::logout();
        } catch (\Exception $e) {
            throw new AuthException('Erro interno ao efetuar logout!', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registrarUsuario(array $data): string
    {
        $senhaCriptografada = bcrypt($data['password']);
        $usuario = User::create([
            ...$data,
            'password' => $senhaCriptografada,
            'plan' => PlanEnum::FREE->value,
            'account_status' => AccountStatusEnum::PENDING->value,
            'payment_status' => PaymentStatusEnum::DUE->value,
            'is_verified' => false,
            'is_active' => true,
        ]);

        if ($usuario) {
            $this->emailVerificationService->enviarVerificacaoEmail($usuario);

            $token = Auth::attempt($data);
            return $token;
        }

        throw new AuthException(
            'Erro ao cadastrar usuário! Por favor, verifique as informações fornecidas e tente novamente.',
            Response::HTTP_BAD_REQUEST
        );
    }

    public function enviarEmailVerificacao(object $data): void
    {
        $usuario = User::getUsuarioPorEmail($data->email);
        $this->emailVerificationService->enviarVerificacaoEmail($usuario);
    }
}
