<?php

namespace Tests\Feature\Services\Reports;

use App\Enums\PortfolioStatus;
use App\Models\Buyer;
use App\Models\Promise;
use App\Services\Reports\AccountStatement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class AccountStatementTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    private Promise $promise;

    private Buyer $buyer;

    /**
     * Six quotas of $100.000 due on the 15th from January 2026, seen on April 20th, with $250.000 paid:
     * the third quota got $50.000.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-20');
        $this->signInToCampaign();

        $this->promise = $this->promiseWithQuotas('PROMESA 7');
        $this->buyer = Buyer::factory()->create(['phone_one' => '573132124855', 'email' => 'PendCorreo@gmail.com', 'address' => 'Calle 10 # 20-30']);
        $this->promise->buyers()->attach($this->buyer);

        $this->pay($this->promise, 100_000, '2026-01-15');
        $this->pay($this->promise, 150_000, '2026-02-15');
    }

    public function test_sums_up_what_was_paid_what_is_owed_and_the_quotas_left(): void
    {
        $statement = AccountStatement::find($this->promise->id, $this->buyer->id);

        $this->assertSame([250_000, 350_000, PortfolioStatus::UP_TO_90], [$statement->paid, $statement->balance, $statement->status]);
        $this->assertSame([150_000, 36], [$statement->standing->overdueAmount, $statement->standing->daysOverdue]);
        $this->assertSame([
            [3, '2026-03-15', 50_000, true],
            [4, '2026-04-15', 100_000, true],
            [5, '2026-05-15', 100_000, false],
            [6, '2026-06-15', 100_000, false],
        ], array_map(fn (array $quota) => [$quota['number'], $quota['due_date']->toDateString(), $quota['pending'], $quota['overdue']], $statement->pendingQuotas));
    }

    public function test_finds_the_promise_from_another_campaign_but_only_for_its_buyers(): void
    {
        $this->signInToCampaign('Otra campaña');
        $stranger = Buyer::factory()->create();

        $statement = AccountStatement::find($this->promise->id, $this->buyer->id);

        $this->assertSame(2, $statement->payments->count());
        $this->assertNull(AccountStatement::find($this->promise->id, $stranger->id));
    }

    public function test_the_pdf_hides_most_of_the_buyer_contact_data(): void
    {
        $html = view('exports.statement', ['statement' => AccountStatement::find($this->promise->id, $this->buyer->id)])->render();

        $this->assertStringContainsString('313•••••55', $html);
        $this->assertStringContainsString('pe••••@gmail.com', $html);
        $this->assertStringNotContainsString('3132124855', $html);
        $this->assertStringNotContainsString('pendcorreo', strtolower($html));
        $this->assertStringNotContainsString('Calle 10', $html);
        $this->assertStringContainsString('$350.000', $html);
    }

    public function test_hides_short_or_missing_contact_data(): void
    {
        $buyer = new Buyer(['phone_one' => '12345', 'email' => 'a@b.co']);

        $this->assertNull($buyer->hiddenPhone());
        $this->assertSame('a••••@b.co', $buyer->hiddenEmail());
        $this->assertNull((new Buyer)->hiddenEmail());
        $this->assertSame('310•••••49', (new Buyer(['phone_one' => '3101234549']))->hiddenPhone());
    }
}
