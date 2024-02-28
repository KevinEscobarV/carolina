<?php

namespace App\Enums;

enum CivilStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWER = 'widower';

    public function getLabel(): string
    {
        return match ($this) {
            self::SINGLE => 'Soltero',
            self::MARRIED => 'Casado',
            self::DIVORCED => 'Divorciado',
            self::WIDOWER => 'Viudo',
        };
    }
}
