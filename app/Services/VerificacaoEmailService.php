<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Exceptions\AuthException;
use App\Exceptions\EmailVerificationException;
use App\Models\VerificacaoEmail;
use App\Models\User;
use App\Notifications\VerificacaoEmailNotification;
use App\Strategies\Emails\IVerificacaoEmailStrategy;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class VerificacaoEmailService implements IVerificacaoEmailService
{
    public function __construct(protected IVerificacaoEmailStrategy $emailVerificationStrategy) {}

    public function sendEmailVerification(User $user): void
    {
        try {
            $url = $this->gerarLinkDeVerificacao($user->email);
            Notification::send($user, new VerificacaoEmailNotification($url));
        } catch (\Throwable $th) {
            throw new EmailVerificationException('Erro interno ao enviar email de verificação!');
        }
    }

    public function verificarEmail(string $email, string $token): bool
    {
        $verificacao = VerificacaoEmail::verificacao($email, $token);
        $this->validarVerificacao($verificacao);

        $usuario = User::marcarEmailComoVerificado($email);
        $usuario->assignRole(RoleEnum::CLIENTE);
        $verificacao->delete();

        return true;
    }

    public function gerarLinkDeVerificacao(string $email): string
    {
        try {
            DB::beginTransaction();

            $verificacao = $this->gerarVerificacao($email, Carbon::now()->addHours(24));
            $url = $this->emailVerificationStrategy->definirUrlDeVerificacao($verificacao->token, $verificacao->email);
            $verificacao->save();

            DB::commit();
            return $url;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new EmailVerificationException('Erro interno ao gerar link de verificação!');
        }
    }

    public static function gerarVerificacao(string $email, $expired_at = null): VerificacaoEmail
    {
        $verificacao = VerificacaoEmail::getVerificacaoPorEmail($email);

        if ($verificacao) {
            $verificacao->delete();
        }

        return new VerificacaoEmail(email: $email, expired_at: $expired_at);
    }

    public function validarVerificacao(VerificacaoEmail|null $verificacao): bool
    {
        if (!$verificacao) {
            throw new AuthException('Token de verificação inexistente!', Response::HTTP_NOT_FOUND);
        }

        if ($verificacao->expired_at < Carbon::now()) {
            $verificacao->delete();
            throw new AuthException('Token de verificação expirado!', Response::HTTP_UNAUTHORIZED);
        }

        return true;
    }
}
