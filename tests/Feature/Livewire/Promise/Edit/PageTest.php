<?php

namespace Tests\Feature\Livewire\Promise\Edit;

use App\Enums\PaymentMethod;
use App\Livewire\Promise\Edit\Page;
use App\Models\Block;
use App\Models\Category;
use App\Models\Parcel;
use App\Models\Payment;
use App\Models\Promise;
use App\Models\User;
use App\Services\Amortization\ScheduleCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_regenerating_keeps_the_paid_quotas_and_spreads_the_rest_over_the_pending_ones(): void
    {
        $category = Category::create(['name' => 'Rivarca']);
        $this->actingAs(User::factory()->create(['current_category_id' => $category->id]));

        $promise = Promise::create([
            'number' => 'P-1',
            'signature_date' => '2026-01-01',
            'value' => 600_000,
            'cut_off_date' => '2026-01-15',
            'payment_frequency' => 'monthly',
            'number_of_fees' => 6,
        ]);
        $promise->saveSchedule(app(ScheduleCalculator::class)->byNumberOfQuotas($promise->scheduleTerms(), 6));
        Payment::create([
            'bill_number' => 'F-1',
            'promise_id' => $promise->id,
            'paid_amount' => 200_000,
            'payment_date' => '2026-02-15',
            'payment_method' => PaymentMethod::cases()[0],
            'is_initial_fee' => false,
        ]);

        $page = Livewire::test(Page::class, ['promise' => $promise->fresh()])
            ->assertOk()
            ->set('form.number_of_fees', 8)
            ->call('project')
            ->assertHasNoErrors();

        $schedule = $page->get('schedule');
        $this->assertCount(8, $schedule);
        $this->assertSame([100_000, 100_000], array_column(array_slice($schedule, 0, 2), 'amount'));
        $this->assertSame(['2026-01-15', '2026-02-15', '2026-03-15'], array_column(array_slice($schedule, 0, 3), 'due_date'));
        $this->assertSame([66_667, 66_667], array_column(array_slice($schedule, 2, 2), 'amount'));
        $this->assertSame(0, end($schedule)['balance']);
    }

    public function test_amounts_reach_the_currency_inputs_as_whole_pesos(): void
    {
        $category = Category::create(['name' => 'Rivarca']);
        $this->actingAs(User::factory()->create(['current_category_id' => $category->id]));

        $promise = Promise::create(['number' => 'P-1', 'signature_date' => '2026-01-01', 'value' => 68_000_000]);
        $block = Block::create(['code' => 'ZM4']);
        $parcel = Parcel::create(['number' => '5', 'block_id' => $block->id, 'value' => 68_000_000]);

        // As MariaDB returns decimal columns: text with a dot, which the inputs would read as thousands
        $promise->setRawAttributes(['value' => '68000000.00', 'initial_fee' => '10000000.00', 'quota_amount' => '1325000.00'] + $promise->getAttributes(), true);

        $page = Livewire::test(Page::class, ['promise' => $promise]);

        $this->assertSame([68_000_000, 10_000_000, 1_325_000], [$page->get('form.value'), $page->get('form.initial_fee'), $page->get('form.quota_amount')]);
        $this->assertSame(68_000_000, $page->set('form.parcels', [$parcel->id])->get('form.value'));
    }
}
