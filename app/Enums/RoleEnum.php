<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MASTER = 'master';
    case GERENTE = 'gerente';
    case FUNCIONARIO = 'funcionario';
    case SECRETARIO = 'secretario';
    case CLIENTE = 'cliente';
}
