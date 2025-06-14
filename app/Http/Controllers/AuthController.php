<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\AutenticatedUserResource;
use App\Models\RefreshToken;
use App\Services\IAuthService;
use App\Services\IUserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(private IAuthService $authService, private IUserService $userService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credenciais = $request->validated();
        $dispositivo = $request->header('User-Agent');

        $tokens = $this->authService->login($credenciais, $dispositivo);

        return ResponseService::success(
            new AutenticatedUserResource(JWTAuth::user(), $tokens['access_token']),
            'Usuario autenticado com sucesso'
        )->cookie($this->makeRefreshCookie($tokens['refresh_token']));
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token') ?? $request->header('X-Refresh-Token');

        if (!$refreshToken) {
            return ResponseService::error(message: 'Refresh token não encontrado.', code: Response::HTTP_UNAUTHORIZED);
        }

        $dispositivo = $request->header('User-Agent');

        $tokens = $this->authService->refresh($refreshToken, $dispositivo);

        return ResponseService::success(
            data: ['token' => $tokens['access_token']],
            message: 'Token renovado com sucesso!',
            code: Response::HTTP_OK
        )->cookie($this->makeRefreshCookie($tokens['refresh_token']));
    }

    public function logout() {}

    public function getDadosUsuarioAutenticado(): JsonResponse
    {
        $user = AutenticatedUserResource::make(JWTAuth::user());
        return ResponseService::success(
            $user,
            'Dados do usuário autenticado retornados com sucesso.',
            Response::HTTP_OK
        );
    }

    private function makeRefreshCookie(string $refreshToken): Cookie
    {
        return Cookie::create(
            'refresh_token',
            $refreshToken,
            time() + 30 * 24 * 60 * 60, // 30 dias em segundos (UNIX timestamp)
            '/',
            'localhost',
            false, // secure (true em produção)
            true, // httpOnly
            false, // raw
            'Lax' // sameSite
        );
    }
}
