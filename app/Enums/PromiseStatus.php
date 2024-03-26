<?php

namespace App\Enums;

enum PromiseStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => '🟡 Pendiente',
            self::CONFIRMED => '🟢 Confirmado',
            self::CANCELLED => '🔴 Cancelado',
        };
    }

    public function text(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'positive',
            self::CANCELLED => 'negative',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'exclamation',
            self::CONFIRMED => 'lock-closed',
            self::CANCELLED => 'ban',
        };
    }

    public static function select(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case,
                'label' => $case->label(),
            ];
        })->toArray();
    }
}
