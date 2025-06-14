<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificacaoEmail;
use App\Attributes\Autobind;

interface IVerificacaoEmailService
{
    public function enviarVerificacaoEmail(User $user): void;
}
