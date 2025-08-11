<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case MANAGER = 'manager';
    case SUPPORT = 'support';
    case FINANCE = 'finance';
    case TECHNICIAN = 'technician';
    case CLIENT = 'client';
}
