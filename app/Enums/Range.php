<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum Range: string
{
    case All_Time = 'all';
    case Year = 'year';
    case Last_30 = 'last30';
    case Last_7 = 'last7';
    case Today = 'today';
    case Custom = 'custom';

    public function label($start = null, $end = null)
    {
        return match ($this) {
            self::All_Time => 'Todo el tiempo',
            self::Year => 'Este año',
            self::Last_30 => 'Últimos 30 días',
            self::Last_7 => 'Últimos 7 días',
            self::Today => 'Hoy',
            self::Custom => ($start !== null && $end !== null)
                ? str($start)->replace('-', '/').' - '.str($end)->replace('-', '/')
                : 'Personalizado',
        };
    }

    public function dates()
    {
        return match ($this) {
            self::Today => [Carbon::today(), now()],
            self::Last_7 => [Carbon::today()->subDays(6), now()],
            self::Last_30 => [Carbon::today()->subDays(29), now()],
            self::Year => [Carbon::now()->startOfYear(), now()],
        };
    }
}
