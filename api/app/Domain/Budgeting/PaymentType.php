<?php

namespace App\Domain\Budgeting;

Enum PaymentType: string
{
    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';
}
