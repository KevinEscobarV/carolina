<?php

namespace Tests\Concerns;

use App\Enums\PaymentMethod;
use App\Enums\PromisePaymentMethod;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Promise;
use App\Models\User;
use App\Services\Amortization\ScheduleCalculator;

/**
 * Promises with a monthly schedule of equal quotas, and payments towards them, for the tests.
 */
trait CreatesPromises
{
    protected Category $campaign;

    protected function signInToCampaign(string $name = 'Rivarca'): User
    {
        $this->campaign = Category::create(['name' => $name]);
        $user = User::factory()->create(['current_category_id' => $this->campaign->id]);
        $this->actingAs($user);

        return $user;
    }

    protected function promiseWithQuotas(string $number, int $quotas = 6, int $quotaAmount = 100_000, string $firstDueDate = '2026-01-15', bool $reducesAmount = false): Promise
    {
        $promise = Promise::create([
            'number' => $number,
            'signature_date' => '2026-01-01',
            'value' => $quotas * $quotaAmount,
            'cut_off_date' => $firstDueDate,
            'payment_frequency' => 'monthly',
            'payment_method' => PromisePaymentMethod::CREDIT,
            'number_of_fees' => $quotas,
            'switch_payment' => $reducesAmount,
        ]);
        $promise->saveSchedule(app(ScheduleCalculator::class)->byNumberOfQuotas($promise->scheduleTerms(), $quotas));

        return $promise;
    }

    protected function pay(Promise $promise, int $amount, string $date = '2026-01-15'): Payment
    {
        return Payment::create([
            'bill_number' => 'F-'.$promise->id.'-'.$amount,
            'promise_id' => $promise->id,
            'paid_amount' => $amount,
            'agreement_amount' => $amount,
            'payment_date' => $date,
            'payment_method' => PaymentMethod::cases()[0],
            'is_initial_fee' => false,
        ]);
    }
}
