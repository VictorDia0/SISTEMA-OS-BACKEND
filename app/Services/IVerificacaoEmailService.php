<?php

namespace App\Services;

use App\Models\User;

interface IVerificacaoEmailService
{
    public function sendEmailVerification(User $user): void;
}
