<?php

namespace App\Services;

use App\Models\User;

interface IVerificacaoEmailService
{
    public function enviarVerificacaoEmail(User $user): void;
}
