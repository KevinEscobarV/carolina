<?php

namespace App\Services\Amortization;

use App\Enums\PaymentFrequency;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * What a schedule is built from: the amount financed, the rate, the dates and the optional
 * extraordinary quotas (a bigger quota in some months of the year, such as June and December).
 */
final readonly class ScheduleTerms
{
    /**
     * @param  int  $principal  amount financed, in pesos (promise value minus initial fee)
     * @param  CarbonImmutable|null  $startDate  when the debt starts (signature); first period of irregular schedules
     * @param  list<int>  $extraordinaryMonths  months (1 to 12) whose quota is the extraordinary amount
     */
    public function __construct(
        public int $principal,
        public CarbonImmutable $firstDueDate,
        public PaymentFrequency $frequency = PaymentFrequency::MONTHLY,
        public InterestRate $rate = new InterestRate,
        public ?CarbonImmutable $startDate = null,
        public array $extraordinaryMonths = [],
        public int $extraordinaryAmount = 0,
    ) {}

    /**
     * Whether the quota due on this date is an extraordinary one.
     */
    public function isExtraordinary(CarbonInterface $date): bool
    {
        return $this->extraordinaryAmount > 0 && in_array($date->month, $this->extraordinaryMonths, true);
    }

    /**
     * Due date a number of periods after another one. Months do not overflow: from January 31
     * come February 28 (or 29) and March 31, because every date counts from the same anchor.
     * Irregular schedules start monthly; their dates are then set by hand.
     */
    public function dueDateAfter(CarbonImmutable $anchor, int $periods): CarbonImmutable
    {
        return match ($this->frequency) {
            PaymentFrequency::WEEKLY => $anchor->addWeeks($periods),
            PaymentFrequency::BIWEEKLY => $anchor->addWeeks(2 * $periods),
            PaymentFrequency::MONTHLY, PaymentFrequency::IRREGULAR => $anchor->addMonthsNoOverflow($periods),
            PaymentFrequency::QUARTERLY => $anchor->addMonthsNoOverflow(3 * $periods),
            PaymentFrequency::SEMI_ANNUAL => $anchor->addMonthsNoOverflow(6 * $periods),
            PaymentFrequency::ANNUAL => $anchor->addMonthsNoOverflow(12 * $periods),
        };
    }

    /**
     * Interest rate of the period that ends on $dueDate. Irregular schedules charge by the days
     * since the previous quota (or since the start of the debt for the first one).
     */
    public function rateFor(CarbonImmutable $dueDate, ?CarbonImmutable $previousDueDate): float
    {
        if ($this->rate->isZero()) {
            return 0.0;
        }

        if ($this->frequency !== PaymentFrequency::IRREGULAR) {
            return $this->rate->forFrequency($this->frequency);
        }

        $from = $previousDueDate ?? $this->startDate ?? $dueDate->subMonthNoOverflow();

        return $this->rate->forDays((int) round($from->diffInDays($dueDate)));
    }
}
