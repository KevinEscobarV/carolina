<?php

namespace Tests\Feature\Livewire\Payment\Create;

use App\Livewire\Payment\Create\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class PageTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    public function test_choosing_a_promise_suggests_what_is_left_of_the_quota_it_is_on(): void
    {
        $this->signInToCampaign();
        $promise = $this->promiseWithQuotas('P-1');
        $this->pay($promise, 150_000);

        Livewire::test(Page::class)
            ->set('form.promise_id', $promise->id)
            ->assertSet('form.agreement_date', '2026-02-15')
            ->assertSet('form.agreement_amount', 100_000)
            ->assertSet('form.paid_amount', 50_000)
            ->assertDispatched('show-amortization', $promise->id);
    }
}
