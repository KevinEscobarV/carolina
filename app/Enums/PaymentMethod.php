<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case CREDIT_CARD = 'card';
    case CHECK = 'check';
    case BANK_TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Efectivo',
            self::CREDIT_CARD => 'Tarjeta de crédito o débito',
            self::CHECK => 'Cheque',
            self::BANK_TRANSFER => 'Transferencia bancaria',
            self::DEPOSIT => 'Depósito',
            self::OTHER => 'Otro',
        };
    }
}
