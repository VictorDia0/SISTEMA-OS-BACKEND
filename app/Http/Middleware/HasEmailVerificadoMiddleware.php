<?php

namespace App\Http\Middleware;

use App\Exceptions\EmailVerificationException;
use App\Services\IVerificacaoEmailService;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasEmailVerificadoMiddleware
{
    public function __construct(private IVerificacaoEmailService $emailService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $credenciais = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credenciais)) {
            $user = Auth::user();

            if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
                $this->emailService->enviarVerificacaoEmail($user);
                throw new EmailVerificationException(
                    'Por favor, verifique seu endereço de email para efetuar o login.',
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        return $next($request);
    }
}
