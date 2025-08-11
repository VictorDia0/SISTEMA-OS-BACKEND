<?php

namespace App\Enums;

enum AccountStatusEnum: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case CLOSED = 'closed';
}
