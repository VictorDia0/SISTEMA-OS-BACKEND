<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AutenticatedUserResource;
use App\Services\IAuthService;
use App\Services\IUserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private int $maxAttempts = 5;

    public function __construct(private IAuthService $authService, private IUserService $userService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credenciais = $request->validated();
        $email = $credenciais['email'];
        $ip = $request->ip();

        $key = "login_attemps:{$email}:{$ip}";
        if (Cache::has($key) && Cache::get($key) >= $this->maxAttempts) {
            return ResponseService::error(
                message: 'Muitas tentativas de login. Tente novamente mais tarde',
                code: Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        $origin = $request->header('Origin') ?? $request->header('Referer');
        if ($origin && !str_starts_with($origin, config('app.url'))) {
            return ResponseService::error(
                message: 'Requisição de origem inválida.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $user = $this->userService->getUserByEmail($email);

        if (!$user || !$user->is_active) {
            return ResponseService::error(
                message: 'Usuário inativo ou não encontrado.',
                code: Response::HTTP_UNAUTHORIZED
            );
        }


        $tokens = $this->authService->login($credenciais, $request->header('User-Agent'));


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

    public function logout(Request $request): JsonResponse
    {
        if (!JWTAuth::check()) {
            return ResponseService::error(
                message: 'Nenhum usuario autenticado',
                code: Response::HTTP_UNAUTHORIZED
            );
        }

        $this->authService->logout($request->header('User-Agent'), $request->ip());

        return ResponseService::success(
            data: [],
            message: 'Logout efetuado com sucesso',
            code: Response::HTTP_OK
        )->withoutCookie($this->makeEmptyRefreshCookie());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $message = $this->authService->register($data);

            return ResponseService::success(
                data: [],
                message: $message,
                code: Response::HTTP_CREATED
            );
        } catch (AuthException $e) {
            return ResponseService::error(
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function getDadosUsuarioAutenticado(): JsonResponse
    {

        return ResponseService::success(
            new AutenticatedUserResource(JWTAuth::user(), JWTAuth::getToken()),
            'Dados do usuário autenticado retornados com sucesso.',
            Response::HTTP_OK
        );
    }

    private function makeRefreshCookie(string $refreshToken): Cookie
    {
        return Cookie::create(
            'refresh_token',
            $refreshToken,
            60 * 24 * 7,
            '/',
            config('session.domain', 'localhost'),
            true,
            true,
            false,
            'Strict'
        );
    }

    private function makeEmptyRefreshCookie(): Cookie
    {
        return Cookie::create(
            'refresh_token',
            '',
            -1,
            '/',
            config('session.domain', 'localhost'),
            true,
            true,
            false,
            'Strict'
        );
    }
}
