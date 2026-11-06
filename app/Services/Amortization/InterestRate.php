<?php

namespace App\Services\Amortization;

use App\Enums\InterestRateType;
use App\Enums\PaymentFrequency;

/**
 * An interest rate as it was agreed (monthly or effective annual) and its equivalent for any period,
 * by compound equivalence: 1 + effective annual = (1 + monthly)^12.
 */
final readonly class InterestRate
{
    public function __construct(
        public float $percent = 0,
        public InterestRateType $type = InterestRateType::MONTHLY,
    ) {}

    public function isZero(): bool
    {
        return $this->percent <= 0;
    }

    /**
     * Effective annual rate as a fraction (0.1956 is 19.56 %).
     */
    public function effectiveAnnual(): float
    {
        return match ($this->type) {
            InterestRateType::EFFECTIVE_ANNUAL => $this->percent / 100,
            InterestRateType::MONTHLY => (1 + $this->percent / 100) ** 12 - 1,
        };
    }

    /**
     * Monthly rate as a fraction.
     */
    public function monthly(): float
    {
        return match ($this->type) {
            InterestRateType::MONTHLY => $this->percent / 100,
            InterestRateType::EFFECTIVE_ANNUAL => (1 + $this->percent / 100) ** (1 / 12) - 1,
        };
    }

    /**
     * Rate for one period of the frequency. Irregular schedules charge by days instead (forDays).
     */
    public function forFrequency(PaymentFrequency $frequency): float
    {
        if ($this->isZero()) {
            return 0.0;
        }

        if (in_array($frequency, [PaymentFrequency::MONTHLY, PaymentFrequency::IRREGULAR], true)) {
            return $this->monthly();
        }

        return (1 + $this->effectiveAnnual()) ** (1 / $frequency->multiplier()) - 1;
    }

    /**
     * Rate for a number of days, over a 365-day year.
     */
    public function forDays(int $days): float
    {
        if ($this->isZero() || $days <= 0) {
            return 0.0;
        }

        return (1 + $this->effectiveAnnual()) ** ($days / 365) - 1;
    }
}
