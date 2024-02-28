<?php

namespace App\Enums;

enum ParcelPosition: string
{
    case POSITION_CORNER = 'corner';
    case POSITION_MIDDLE = 'middle';

    public function getLabel(): string
    {
        return match ($this) {
            self::POSITION_CORNER => 'Esquinero',
            self::POSITION_MIDDLE => 'Medianero',
        };
    }
}
