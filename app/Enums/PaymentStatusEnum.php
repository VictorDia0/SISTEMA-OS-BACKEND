<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PAID = 'pago';
    case DUE = 'vencido';
    case PENDING = 'pendente';
    case OVERDUE = 'atrasado';
}
