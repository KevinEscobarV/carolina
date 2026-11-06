<?php

namespace Tests\Feature\Livewire\Promise\Review;

use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Livewire\Promise\Review\Page;
use App\Models\Promise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class PageTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signInToCampaign();
    }

    public function test_lists_the_schedules_to_review_and_leaves_out_the_right_and_the_cancelled_ones(): void
    {
        $this->promiseWithQuotas('P-BIEN');
        $this->promiseWithQuotas('P-FALTA')->update(['value' => 700_000]);
        $this->promiseWithQuotas('P-CANCELADA')->update(['value' => 700_000, 'status' => PromiseStatus::CANCELLED]);
        Promise::create(['number' => 'P-SIN', 'signature_date' => '2026-01-01', 'value' => 1_000_000, 'payment_method' => PromisePaymentMethod::CREDIT]);
        Promise::create(['number' => 'P-CONTADO', 'signature_date' => '2026-01-01', 'value' => 1_000_000, 'payment_method' => PromisePaymentMethod::CASH]);

        $reviews = Livewire::test(Page::class)->viewData('reviews');

        // The biggest difference first: without a schedule the whole balance is missing
        $this->assertSame(
            ['P-SIN' => ['Sin proyección'], 'P-FALTA' => ['Faltan $100.000']],
            $reviews->mapWithKeys(fn (array $review) => [$review['promise']->number => array_column($review['issues'], 'label')])->all(),
        );
    }

    public function test_reports_dates_out_of_order(): void
    {
        $promise = $this->promiseWithQuotas('P-FECHAS');
        $promise->quotas()->where('number', 3)->update(['due_date' => '2025-12-01']);

        Livewire::test(Page::class)
            ->set('issue', 'unordered_dates')
            ->assertSee('P-FECHAS')
            ->assertSee('Cuota 3 antes que la anterior');
    }
}
