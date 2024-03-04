<?php

namespace App\Enums;

enum PaymentFrequency: string
{
    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case SEMI_ANNUAL = 'semi_annual';
    case ANNUAL = 'annual';
    case IRREGULAR = 'irregular';

    public function label(): string
    {
        return match ($this) {
            self::WEEKLY => 'Semanal',
            self::BIWEEKLY => 'Quincenal',
            self::MONTHLY => 'Mensual',
            self::QUARTERLY => 'Trimestral',
            self::SEMI_ANNUAL => 'Semestral',
            self::ANNUAL => 'Anual',
            self::IRREGULAR => 'Irregular',
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

    public function multiplier(): int
    {
        return match ($this) {
            self::WEEKLY => 52,
            self::BIWEEKLY => 26,
            self::MONTHLY => 12,
            self::QUARTERLY => 4,
            self::SEMI_ANNUAL => 2,
            self::ANNUAL => 1,
            self::IRREGULAR => 1,
        };
    }

    public static function implode(): string
    {
        return collect(self::cases())->map(function ($case) {
            return '\'' . $case->value . '\''; // Add quotes to the value
        })->implode(',');
    }
}
