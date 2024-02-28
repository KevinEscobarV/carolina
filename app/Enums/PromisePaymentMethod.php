<?php

namespace App\Enums;

enum PromisePaymentMethod: string
{
    case CASH = 'cash';
    case CREDIT = 'credit';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Contado',
            self::CREDIT => 'Crédito',
        };
    }
}
