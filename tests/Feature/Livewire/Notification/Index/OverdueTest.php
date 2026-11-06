<?php

namespace Tests\Feature\Livewire\Notification\Index;

use App\Livewire\Notification\Index\Overdue;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class OverdueTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signInToCampaign();
    }

    public function test_lists_promises_behind_by_amount_and_not_those_that_paid_several_quotas_at_once(): void
    {
        Carbon::setTestNow('2026-04-10');

        // One payment covering January to March: up to date, though it is a single payment
        $paidAhead = $this->promiseWithQuotas('P-AL-DIA');
        $this->pay($paidAhead, 300_000);

        $behind = $this->promiseWithQuotas('P-EN-MORA');
        $this->pay($behind, 100_000);

        $promises = Livewire::test(Overdue::class)->viewData('promises');

        $this->assertSame(['P-EN-MORA'], $promises->pluck('number')->all());
        $this->assertSame(200_000, $promises->first()->standing()->overdueAmount);
        $this->assertSame(54, $promises->first()->standing()->daysOverdue);
    }

    public function test_the_grace_days_of_the_campaign_delay_the_arrears(): void
    {
        Carbon::setTestNow('2026-01-20');
        $this->promiseWithQuotas('P-1');
        Setting::create(['key' => 'grace_days', 'value' => '10']);

        $this->assertCount(0, Livewire::test(Overdue::class)->viewData('promises'));
    }
}
