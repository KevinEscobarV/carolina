<?php

namespace App\Services\Reports;

use App\Enums\PortfolioStatus;
use App\Models\Buyer;
use App\Models\Payment;
use App\Models\Promise;
use App\Models\Scopes\CategoryScope;
use App\Models\Setting;
use App\Services\Amortization\Standing;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Account statement of a promise for one of its buyers, as downloaded from the public page: what was
 * paid, what is owed, whether it is up to date and the quotas still to pay. Campaign filters are left
 * out, because the buyer is not signed in and a staff member may be in another campaign.
 */
class AccountStatement
{
    public readonly CarbonImmutable $today;

    public readonly Standing $standing;

    public readonly PortfolioStatus $status;

    public readonly int $paid;

    public readonly int $balance;

    /**
     * @var Collection<int, Payment>
     */
    public readonly Collection $payments;

    /**
     * @var list<array{number: int, due_date: CarbonImmutable, amount: int, pending: int, overdue: bool}>
     */
    public readonly array $pendingQuotas;

    public function __construct(public readonly Promise $promise, public readonly Buyer $buyer)
    {
        $this->today = CarbonImmutable::today();
        $this->standing = $promise->standing();
        $this->payments = $promise->paymentsUntil();
        $this->paid = (int) round((float) $this->payments->sum('paid_amount'));
        $this->balance = max(0, (int) round((float) $promise->value) - $this->paid);
        $this->status = PortfolioStatus::of($this->standing, $promise->quotas->isNotEmpty(), $this->balance);
        $this->pendingQuotas = $this->pendingQuotas();
    }

    /**
     * The promise and the buyer, when the buyer is one of its owners.
     */
    public static function find(int $promiseId, int $buyerId): ?self
    {
        $withoutCampaign = fn (Relation $query) => $query->withoutGlobalScope(CategoryScope::class);

        $promise = Promise::withoutGlobalScope(CategoryScope::class)
            ->with([
                'buyers' => $withoutCampaign,
                'payments' => $withoutCampaign,
                'parcels' => fn (Relation $query) => $query->withoutGlobalScope(CategoryScope::class)->with(['block' => $withoutCampaign]),
                'quotas',
                'category',
            ])
            ->find($promiseId);

        $buyer = $promise?->buyers->firstWhere('id', $buyerId);

        return $buyer ? new self($promise, $buyer) : null;
    }

    /**
     * The statement as a PDF, with "Página X de Y" at the bottom right of every page: DomPDF only knows
     * the page count once the document is laid out.
     */
    public function pdf(): DomPdf
    {
        $pdf = Pdf::loadView('exports.statement', ['statement' => $this]);
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $metrics = $dompdf->getFontMetrics();
        $font = $metrics->getFont('Helvetica');
        $size = 6.75;
        $margin = 42.52; // 1.5 cm, the side margin of the page

        $canvas->page_text(
            $canvas->get_width() - $margin - $metrics->getTextWidth('Página 00 de 00', $font, $size),
            $canvas->get_height() - 40.2,
            'Página {PAGE_NUM} de {PAGE_COUNT}',
            $font,
            $size,
            [0.42, 0.45, 0.5],
        );

        return $pdf;
    }

    /**
     * Lots of the promise, as block:number.
     */
    public function lots(): string
    {
        return $this->promise->parcels
            ->map(fn ($parcel) => ($parcel->block?->code ? $parcel->block->code.':' : '').$parcel->number)
            ->implode(', ');
    }

    /**
     * Quotas from the one in course on: the first one may be partly paid; the late ones are marked.
     *
     * @return list<array{number: int, due_date: CarbonImmutable, amount: int, pending: int, overdue: bool}>
     */
    private function pendingQuotas(): array
    {
        if ($this->standing->currentIndex === null) {
            return [];
        }

        $lateBefore = $this->today->subDays(Setting::graceDays($this->promise->category_id))->toDateString();
        $rows = array_slice($this->promise->scheduleRows(), $this->standing->currentIndex);

        return array_map(fn (array $row, int $index) => [
            'number' => (int) $row['number'],
            'due_date' => CarbonImmutable::parse($row['due_date']),
            'amount' => (int) $row['amount'],
            'pending' => $index === 0 ? $this->standing->currentPending : (int) $row['amount'],
            'overdue' => $row['due_date'] < $lateBefore,
        ], $rows, array_keys($rows));
    }
}
