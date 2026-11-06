<?php

namespace Tests\Unit\Services\Amortization;

use App\Enums\InterestRateType;
use App\Enums\PaymentFrequency;
use App\Services\Amortization\InterestRate;
use PHPUnit\Framework\TestCase;

class InterestRateTest extends TestCase
{
    public function test_monthly_rate_converts_to_its_effective_annual_equivalent(): void
    {
        $rate = new InterestRate(1.5, InterestRateType::MONTHLY);

        $this->assertEqualsWithDelta(0.195618, $rate->effectiveAnnual(), 0.000001);
    }

    public function test_effective_annual_rate_converts_to_its_monthly_equivalent(): void
    {
        $rate = new InterestRate(24, InterestRateType::EFFECTIVE_ANNUAL);

        $this->assertEqualsWithDelta(0.018088, $rate->monthly(), 0.000001);
    }

    public function test_monthly_rate_is_used_as_is_for_monthly_quotas(): void
    {
        $rate = new InterestRate(1.2, InterestRateType::MONTHLY);

        $this->assertSame(0.012, $rate->forFrequency(PaymentFrequency::MONTHLY));
    }

    public function test_quarterly_rate_compounds_three_monthly_periods(): void
    {
        $rate = new InterestRate(1, InterestRateType::MONTHLY);

        $this->assertEqualsWithDelta(1.01 ** 3 - 1, $rate->forFrequency(PaymentFrequency::QUARTERLY), 0.0000001);
    }

    public function test_rate_for_days_compounds_the_effective_annual_rate_over_a_365_day_year(): void
    {
        $rate = new InterestRate(12, InterestRateType::EFFECTIVE_ANNUAL);

        $this->assertEqualsWithDelta(0.12, $rate->forDays(365), 0.0000001);
        $this->assertEqualsWithDelta(1.12 ** (30 / 365) - 1, $rate->forDays(30), 0.0000001);
        $this->assertSame(0.0, $rate->forDays(0));
    }

    public function test_zero_rate_charges_nothing_in_any_period(): void
    {
        $rate = new InterestRate(0);

        $this->assertSame(0.0, $rate->forFrequency(PaymentFrequency::QUARTERLY));
        $this->assertSame(0.0, $rate->forDays(90));
    }
}
