<?php

namespace App\Enums;

use App\Services\Amortization\Standing;

/**
 * Where a promise stands in the portfolio: up to date, late by how many days, paid off, or something to
 * review (every quota covered and money still owed, or no schedule while money is owed).
 */
enum PortfolioStatus: string
{
    case UP_TO_DATE = 'up_to_date';
    case UP_TO_30 = 'up_to_30';
    case UP_TO_90 = 'up_to_90';
    case OVER_90 = 'over_90';
    case PAID = 'paid';
    case CHECK_BALANCE = 'check_balance';
    case NO_SCHEDULE = 'no_schedule';

    /**
     * Status of a promise by its standing and the balance it still owes.
     */
    public static function of(Standing $standing, bool $hasSchedule, int $balance): self
    {
        $owesMoney = $balance >= Standing::TOLERANCE;

        return match (true) {
            ! $hasSchedule => $owesMoney ? self::NO_SCHEDULE : self::PAID,
            $standing->currentQuota === null => $owesMoney ? self::CHECK_BALANCE : self::PAID,
            default => self::byDaysOverdue($standing),
        };
    }

    /**
     * Up to date or late by how many days, for promises with a schedule.
     */
    public static function byDaysOverdue(Standing $standing): self
    {
        return match (true) {
            ! $standing->isOverdue() => self::UP_TO_DATE,
            $standing->daysOverdue <= 30 => self::UP_TO_30,
            $standing->daysOverdue <= 90 => self::UP_TO_90,
            default => self::OVER_90,
        };
    }

    /**
     * The statuses of the arrears report, from up to date to the most overdue.
     *
     * @return list<self>
     */
    public static function aging(): array
    {
        return [self::UP_TO_DATE, self::UP_TO_30, self::UP_TO_90, self::OVER_90];
    }

    public function label(): string
    {
        return match ($this) {
            self::UP_TO_DATE => 'Al día',
            self::UP_TO_30 => '1 a 30 días',
            self::UP_TO_90 => '31 a 90 días',
            self::OVER_90 => 'Más de 90 días',
            self::PAID => 'Pagada',
            self::CHECK_BALANCE => 'Revisar saldo',
            self::NO_SCHEDULE => 'Sin proyección',
        };
    }

    public function isOverdue(): bool
    {
        return in_array($this, [self::UP_TO_30, self::UP_TO_90, self::OVER_90], true);
    }

    /**
     * Light fill for the status cell of the Excel reports (ARGB without alpha).
     */
    public function fill(): string
    {
        return match ($this) {
            self::UP_TO_DATE => 'DCF2DC',
            self::UP_TO_30 => 'FEF0CC',
            self::UP_TO_90 => 'FBE0D4',
            self::OVER_90 => 'F5D0D0',
            self::PAID => 'DCE8F8',
            self::CHECK_BALANCE, self::NO_SCHEDULE => 'E5E7EB',
        };
    }
}
