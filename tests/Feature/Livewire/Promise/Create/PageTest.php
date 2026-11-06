<?php

namespace Tests\Feature\Livewire\Promise\Create;

use App\Enums\PaymentFrequency;
use App\Livewire\Promise\Create\Page;
use App\Models\Block;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\Parcel;
use App\Models\Promise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Rivarca']);
        $this->actingAs(User::factory()->create(['current_category_id' => $category->id]));
    }

    public function test_saving_stores_the_generated_schedule_with_its_extraordinary_quotas(): void
    {
        $this->agreement(12_000_000, quotas: 12)
            ->set('form.has_extraordinary', true)
            ->set('form.extraordinary_months', ['6', '12'])
            ->set('form.extraordinary_amount', 2_000_000)
            ->call('project')
            ->assertSet('schedule.0.amount', 800_000)
            ->assertSet('schedule.5.amount', 2_000_000)
            ->call('save')
            ->assertHasNoErrors();

        $promise = Promise::where('number', 'P-1')->firstOrFail();
        $this->assertSame(12, $promise->quotas()->count());
        $this->assertSame(12_000_000, (int) $promise->quotas()->sum('amount'));
        $this->assertSame([6, 12], $promise->extraordinary_months);
        $this->assertSame(800_000, $promise->projection[0]['payment_amount']);
        $this->assertSame(1, Parcel::where('promise_id', $promise->id)->count());
    }

    /**
     * @return array<string, array{string, list<int>}>
     */
    public static function rebalanceModes(): array
    {
        return [
            'reduce the number of quotas' => ['0', [100_000, 200_000, 100_000, 100_000, 100_000]],
            'reduce the value of the quotas' => ['1', [100_000, 200_000, 75_000, 75_000, 75_000, 75_000]],
        ];
    }

    /**
     * @param  list<int>  $expected
     */
    #[DataProvider('rebalanceModes')]
    public function test_changing_a_quota_adjusts_the_following_ones_as_the_promise_chose(string $switchPayment, array $expected): void
    {
        $page = $this->agreement(600_000, quotas: 6)
            ->set('form.switch_payment', $switchPayment)
            ->call('project')
            ->set('schedule.1.amount', 200_000);

        $this->assertSame($expected, array_column($page->get('schedule'), 'amount'));
    }

    public function test_a_due_date_before_the_previous_quota_is_rejected_and_the_schedule_is_kept(): void
    {
        $this->agreement(600_000, quotas: 6)
            ->set('form.payment_frequency', PaymentFrequency::IRREGULAR->value)
            ->call('project')
            ->set('schedule.2.due_date', '2025-01-01')
            ->assertHasErrors('schedule')
            ->assertSet('schedule.2.due_date', '2026-03-15');
    }

    private function agreement(int $value, int $quotas): Testable
    {
        $parcel = Parcel::create(['number' => 1, 'block_id' => Block::create(['code' => 'M1'])->id, 'value' => $value]);

        return Livewire::test(Page::class)
            ->set('form.number', 'P-1')
            ->set('form.buyers', [Buyer::factory()->create()->id])
            ->set('form.parcels', [$parcel->id])
            ->set('form.cut_off_date', '2026-01-15')
            ->set('form.number_of_fees', $quotas);
    }
}
