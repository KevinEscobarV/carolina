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
            static::All_Time => 'Todo el tiempo',
            static::Year => 'Este año',
            static::Last_30 => 'Últimos 30 días',
            static::Last_7 => 'Últimos 7 días',
            static::Today => 'Hoy',
            static::Custom => ($start !== null && $end !== null)
                ? str($start)->replace('-', '/') . ' - ' . str($end)->replace('-', '/')
                : 'Personalizado',
        };
    }

    public function dates()
    {
        return match ($this) {
            static::Today => [Carbon::today(), now()],
            static::Last_7 => [Carbon::today()->subDays(6), now()],
            static::Last_30 => [Carbon::today()->subDays(29), now()],
            static::Year => [Carbon::now()->startOfYear(), now()],
        };
    }
}
