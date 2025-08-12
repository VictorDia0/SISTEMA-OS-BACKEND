<?php

namespace App\Services;

interface IAuthService
{
    public function login(array $credenciais, string $dispositivo): array;
    public function refresh(string $token, string $dispositivo): array;
    public function logout(): void;
    public function registrarUsuario(array $data): string;
    public function enviarEmailVerificacao(object $data): void;
    public function verificarEmail(object $data): void;
    public function redefinirSenha(object $data): void;
}
