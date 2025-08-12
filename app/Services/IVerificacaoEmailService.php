<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificacaoEmail;

interface IVerificacaoEmailService
{
    public function enviarVerificacaoEmail(User $user): void;
    public function verificarEmail(string $email, string $token): bool;
    public function gerarLinkDeVerificacao(string $email): string;
    public static function gerarVerificacao(string $email, $expired_at = null): VerificacaoEmail;
    public function validarVerificacao(VerificacaoEmail $verificacao): bool;
}
