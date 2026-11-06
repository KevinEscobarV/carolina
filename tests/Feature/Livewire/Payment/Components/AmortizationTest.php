<?php

namespace Tests\Feature\Livewire\Payment\Components;

use App\Enums\PaymentMethod;
use App\Livewire\Payment\Components\Amortization;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Promise;
use App\Models\User;
use App\Services\Amortization\ScheduleCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class AmortizationTest extends TestCase
{
    use RefreshDatabase;

    private Promise $promise;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Rivarca']);
        $this->actingAs(User::factory()->create(['current_category_id' => $category->id]));

        $this->promise = Promise::create([
            'number' => 'P-1',
            'signature_date' => '2026-01-01',
            'value' => 600_000,
            'cut_off_date' => '2026-01-15',
            'payment_frequency' => 'monthly',
            'switch_payment' => true,
        ]);
        $this->promise->saveSchedule(app(ScheduleCalculator::class)->byNumberOfQuotas($this->promise->scheduleTerms(), 6));

        // Covers the first quota and half of the second
        Payment::create([
            'bill_number' => 'F-1',
            'promise_id' => $this->promise->id,
            'paid_amount' => 150_000,
            'payment_date' => '2026-01-15',
            'payment_method' => PaymentMethod::cases()[0],
            'is_initial_fee' => false,
        ]);
    }

    public function test_a_paid_quota_cannot_be_changed(): void
    {
        $this->panel()
            ->set('schedule.0.amount', 50_000)
            ->assertHasErrors('schedule')
            ->assertSet('schedule.0.amount', 100_000);
    }

    public function test_saving_stores_the_changed_quotas_without_payment_details(): void
    {
        $this->panel()
            ->set('schedule.1.amount', 200_000)
            ->assertHasNoErrors()
            ->call('save');

        $this->assertSame(
            [100_000, 200_000, 75_000, 75_000, 75_000, 75_000],
            $this->promise->quotas()->pluck('amount')->all(),
        );
        $this->assertSame(
            ['number', 'due_date', 'payment_amount', 'interest_payment', 'principal_payment', 'remaining_balance'],
            array_keys($this->promise->fresh()->projection[1]),
        );
    }

    private function panel(): Testable
    {
        return Livewire::test(Amortization::class)->call('setProjection', $this->promise->id);
    }
}
