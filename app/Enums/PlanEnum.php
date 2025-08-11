<?php

namespace App\Enums;

enum PlanEnum: string
{
    case FREE = 'free';
    case BASIC = 'basic';
    case MEDIUM = 'medium';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';
}
