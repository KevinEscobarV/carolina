<?php

namespace App\Enums;

enum PromisePaymentMethod: string
{
    case CASH = 'cash';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Contado',
            self::CREDIT => 'Crédito',
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
