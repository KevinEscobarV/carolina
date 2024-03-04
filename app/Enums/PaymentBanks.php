<?php

namespace App\Enums;

enum PaymentBanks: string
{
    case BANCOLOMBIA = 'Bancolombia';
    case DAVIVIENDA = 'Davivienda';
    case BANCO_DE_OCCIDENTE = 'Banco de Occidente';
    case BANCO_POPULAR = 'Banco Popular';
    case BANCO_DE_BOGOTA = 'Banco de Bogotá';
    case BANCO_AV_VILLAS = 'Banco AV Villas';
    case BANCO_BBVA = 'Banco BBVA';
    case BANCO_ITAU = 'Banco Itaú';
    case BANCO_CAJA_SOCIAL = 'Banco Caja Social';
    case BANCO_COLPATRIA = 'Banco Colpatria';
    case BANCO_GNB_SUDAMERIS = 'Banco GNB Sudameris';
    case BANCO_FALABELLA = 'Banco Falabella';
    case BANCO_PICHINCHA = 'Banco Pichincha';
    case BANCO_AGRARIO = 'Banco Agrario';
    case BANCO_COOPERATIVO = 'Banco Cooperativo';
    case BANCO_FINANDINA = 'Banco Finandina';
    case BANCO_MUNDO_MUJER = 'Banco Mundo Mujer';
    case BANCO_W = 'Banco W';
    case BANCOOMEVA = 'Bancoomeva';
    case BANCO_SERFINANZA = 'Banco Serfinanza';
    case BANCO_PATRIMONIO = 'Banco Patrimonio';
    case BANCO_PSE = 'Banco PSE';

    public function label(): string
    {
        return match ($this) {
            self::BANCOLOMBIA => 'Bancolombia',
            self::DAVIVIENDA => 'Davivienda',
            self::BANCO_DE_OCCIDENTE => 'Banco de Occidente',
            self::BANCO_POPULAR => 'Banco Popular',
            self::BANCO_DE_BOGOTA => 'Banco de Bogotá',
            self::BANCO_AV_VILLAS => 'Banco AV Villas',
            self::BANCO_BBVA => 'Banco BBVA',
            self::BANCO_ITAU => 'Banco Itaú',
            self::BANCO_CAJA_SOCIAL => 'Banco Caja Social',
            self::BANCO_COLPATRIA => 'Banco Colpatria',
            self::BANCO_GNB_SUDAMERIS => 'Banco GNB Sudameris',
            self::BANCO_FALABELLA => 'Banco Falabella',
            self::BANCO_PICHINCHA => 'Banco Pichincha',
            self::BANCO_AGRARIO => 'Banco Agrario',
            self::BANCO_COOPERATIVO => 'Banco Cooperativo',
            self::BANCO_FINANDINA => 'Banco Finandina',
            self::BANCO_MUNDO_MUJER => 'Banco Mundo Mujer',
            self::BANCO_W => 'Banco W',
            self::BANCOOMEVA => 'Bancoomeva',
            self::BANCO_SERFINANZA => 'Banco Serfinanza',
            self::BANCO_PATRIMONIO => 'Banco Patrimonio',
            self::BANCO_PSE => 'Banco PSE',
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
