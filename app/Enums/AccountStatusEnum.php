<?php

namespace App\Enums;

enum AccountStatusEnum: string
{
    case ACTIVE = 'ativo';
    case PENDING = 'pendente';
    case SUSPENDED = 'suspenso';
}
