<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Models\RefreshToken;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements IAuthService
{
    public function login(array $credenciais, string $dispositivo): array
    {
        $token = JWTAuth::attempt($credenciais);

        if (!$token) {
            throw new AuthException('Usuario ou senha inválidos', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $refreshToken = RefreshToken::make(JWTAuth::user(), $dispositivo);

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
}
