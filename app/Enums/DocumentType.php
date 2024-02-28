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

    public function getLabel(): string
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
}
