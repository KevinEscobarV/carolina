<?php

namespace App\Enums;

enum DocumentType: string
{
    case CC = 'cc';
    case CE = 'ce';
    case TI = 'ti';
    case NIT = 'nit';
    case RUT = 'rut';
    case PASSPORT = 'passport';

    public function label(): string
    {
        return match ($this) {
            self::CC => 'Cédula de ciudadanía',
            self::CE => 'Cédula de extranjería',
            self::TI => 'Tarjeta de identidad',
            self::NIT => 'NIT',
            self::RUT => 'RUT',
            self::PASSPORT => 'Pasaporte',
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
