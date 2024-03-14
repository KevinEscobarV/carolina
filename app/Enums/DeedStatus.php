<?php

namespace App\Enums;

enum DeedStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => '🟡 Pendiente',
            self::PAID => '🟢 Realizada',
            self::CANCELLED => '🔴 Cancelado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'positive',
            self::CANCELLED => 'negative',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'exclamation',
            self::PAID => 'lock-closed',
            self::CANCELLED => 'ban',
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
}
