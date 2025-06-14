<?php

namespace App\Enums;

enum PlanEnum: string
{
    case FREE = 'gratis';
    case BASIC = 'basico';
    case MEDIUM = 'medio';
    case PRO = 'pro';
}
