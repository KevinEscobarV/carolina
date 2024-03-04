<?php

namespace App\Enums;

enum CivilStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWER = 'widower';
    case FREE_UNION = 'free_union';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE => 'Soltero',
            self::MARRIED => 'Casado',
            self::DIVORCED => 'Divorciado',
            self::WIDOWER => 'Viudo',
            self::FREE_UNION => 'Unión libre',
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
