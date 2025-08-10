<?php

namespace App\Strategies\Emails;

class VerificacaoEmailStrategy implements IVerificacaoEmailStrategy
{
    private $basePath;
    private $baseFrontendUrl;
    public function __construct()
    {
        $this->basePath = config('app.url');
        $this->baseFrontendUrl = config('app.frontend_url');
    }
    public function definirUrlDeVerificacao(string $token, string $email): string
    {
        return $this->basePath . "/api/auth/verify?token={$token}&email={$email}";
    }

    public function definirUrlRedefinirSenha(string $token, string $email): string
    {
        return $this->baseFrontendUrl . "/reset-password?token={$token}&email={$email}";
    }
}
