<?php

namespace App\Enums;

enum InterestRateType: string
{
    case MONTHLY = 'monthly';
    case EFFECTIVE_ANNUAL = 'effective_annual';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Mensual',
            self::EFFECTIVE_ANNUAL => 'Efectiva anual (E.A.)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::MONTHLY => 'mensual',
            self::EFFECTIVE_ANNUAL => 'E.A.',
        };
    }

    public static function select(): array
    {
        return collect(self::cases())->map(fn (self $case) => [
            'value' => $case,
            'label' => $case->label(),
        ])->toArray();
    }
}
