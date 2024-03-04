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

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Efectivo',
            self::CREDIT_CARD => 'Tarjeta de crédito o débito',
            self::CHECK => 'Cheque',
            self::BANK_TRANSFER => 'Transferencia bancaria',
            self::DEPOSIT => 'Consignación',
            self::OTHER => 'Otro',
        };
    }

    public static function select(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case,
                'label'=> $case->label(),
            ];
        })->toArray();
    }

    public static function implode(): string
    {
        return collect(self::cases())->map(function ($case) {
            return '\'' . $case->value . '\''; // Add quotes to the value
        })->implode(',');
    }
}
