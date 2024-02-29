<?php

namespace App\Enums;

enum ParcelPosition: string
{
    case POSITION_CORNER = 'corner';
    case POSITION_MIDDLE = 'middle';

    public function label(): string
    {
        return match ($this) {
            self::POSITION_CORNER => 'Esquinero',
            self::POSITION_MIDDLE => 'Medianero',
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
