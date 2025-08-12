<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Http\Requests\VerificarEmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AutenticatedUserResource;
use App\Models\RefreshToken;
use App\Services\IAuthService;
use App\Services\IUserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends Controller
{
    public function __construct(private IAuthService $authService, private IUserService $userService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credenciais = $request->validated();

        $device = $request->header('User-Agent');

        $tokens = $this->authService->login($credenciais, $device);

        $data = AutenticatedUserResource::make(
            Auth::user(),
            $tokens['access_token']
        )->toArray($request);

        return ResponseService::success(
            array_merge(
                $data,
                ['message' => 'Usuário autenticado com sucesso']
            )
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
        $this->authService->logout();

        return ResponseService::success(
            data: [],
            message: 'Logout realizado com sucesso!',
            code: Response::HTTP_OK
        )->cookie($this->makeRefreshCookie());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $message = $this->authService->registrarUsuario($data);

            return ResponseService::success(data: [], message: $message, code: Response::HTTP_CREATED);
        } catch (AuthException $e) {
            return ResponseService::error(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function enviarEmailVerificacao(VerificarEmailRequest $request): JsonResponse
    {
        $data = (object) $request->validated();

        $this->authService->enviarEmailVerificacao($data);
        return ResponseService::success(
            data: [],
            message: 'Email de verificação enviado com sucesso!',
            code: Response::HTTP_OK
        );
    }

    public function verificarEmailUsuario(VerificarEmailRequest $request): RedirectResponse
    {
        $data = (object) $request->validated();
        $this->authService->verificarEmail($data);
        return redirect()->to(env('FRONTEND_URL'));
    }

    public function getDadosUsuarioAutenticado(): JsonResponse
    {
        return ResponseService::success(
            new AutenticatedUserResource(Auth::user(), Auth::getToken()),
            'Dados do usuário autenticado retornados com sucesso.',
            Response::HTTP_OK
        );
    }

    private function makeRefreshCookie(RefreshToken $refreshToken = null, string $value = '', int $ttl = 60): Cookie
    {
        $token = !is_null($refreshToken) ? $refreshToken->token : $value;
        $ttl = !is_null(value: $refreshToken) ? $refreshToken->getTTL() : $ttl;
        return Cookie::create('refresh_token', $token, time() + $ttl, '/', null, false, true, false);
    }
}
