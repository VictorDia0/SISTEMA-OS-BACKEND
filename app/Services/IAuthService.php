<?php

namespace App\Services;

interface IAuthService
{
    public function login(array $credenciais, string $dispositivo): array;
    public function refresh(string $token, string $dispositivo): array;
    public function logout(): void;
    public function register(array $data): string;
}
