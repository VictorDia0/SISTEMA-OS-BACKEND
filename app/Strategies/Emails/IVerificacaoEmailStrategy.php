<?php

namespace App\Strategies\Emails;

interface IVerificacaoEmailStrategy
{
    public function definirUrlDeVerificacao(string $token, string $email): string;
    public function definirUrlRedefinirSenha(string $token, string $email): string;
}
