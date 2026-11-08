<?php

namespace App\Exports\Promise;

use App\Models\Buyer;
use App\Models\Parcel;
use App\Models\Payment;
use App\Services\Reports\PortfolioStatement;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * One row per promise: first the last payment, in the order the administration asked for (the payments of
 * that day together: receipts joined, amounts added), then where the promise stands on the cut-off date.
 * Totals follow the autofilter.
 */
class WalletStatusDetail extends DefaultValueBinder implements FromArray, WithColumnFormatting, WithColumnWidths, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithStrictNullComparison, WithTitle
{
    public const MONEY = '"$"#,##0';

    public const DATE = 'dd/mm/yyyy';

    private const FIRST_ROW = 3;

    /**
     * Receipts, promise numbers, documents and phones: kept as text so Excel neither drops digits nor
     * shows them in scientific notation.
     */
    private const TEXT_COLUMNS = ['A', 'G', 'L', 'M'];

    public function __construct(
        private readonly PortfolioStatement $statement,
        private readonly string $campaign,
    ) {}

    public function bindValue(Cell $cell, mixed $value): bool
    {
        if ($value !== null && $cell->getRow() >= self::FIRST_ROW && in_array($cell->getColumn(), self::TEXT_COLUMNS, true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function title(): string
    {
        return 'Estado de cartera';
    }

    public function startCell(): string
    {
        return 'A2';
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'N° recibo', 'Monto pactado', 'Fecha de pago', 'Monto pagado', 'Banco', 'Método de pago', 'N° promesa',
            'Lotes', 'Compradores', 'Fecha pactada', 'Observaciones',
            'Documento', 'Teléfono', 'Valor promesa', 'Total pagado', 'Saldo', 'Cuotas pagadas',
            'Fecha cuota pendiente', 'Valor cuota pendiente', 'Valor en mora', 'Días de mora', 'Estado',
        ];
    }

    /**
     * @return list<list<mixed>>
     */
    public function array(): array
    {
        return $this->statement->entries->map(function (array $entry) {
            $promise = $entry['promise'];
            $payments = $entry['lastPayments'];
            $paidSomething = $payments->isNotEmpty();
            $standing = $entry['standing'];
            $quotas = $promise->quotas->count();

            return [
                $paidSomething ? $this->join($payments->pluck('bill_number')) : 'Sin pagos',
                $payments->whereNotNull('agreement_amount')->isEmpty() ? null : (float) $payments->sum('agreement_amount'),
                $this->excelDate($payments->first()?->payment_date),
                $paidSomething ? (float) $payments->sum('paid_amount') : null,
                $this->join($payments->map(fn (Payment $payment) => $payment->bank ?: 'No aplica')),
                $this->join($payments->map(fn (Payment $payment) => $payment->payment_method?->label())),
                $promise->number,
                $promise->parcels->map(fn (Parcel $parcel) => $parcel->block?->code.':'.$parcel->number)->implode(', '),
                $promise->buyers->map(fn (Buyer $buyer) => $this->clean($buyer->names.' '.$buyer->surnames))->implode(', '),
                $this->excelDate($payments->pluck('agreement_date')->filter()->max()),
                $this->join($payments->map(fn (Payment $payment) => $this->clean((string) $payment->observations)), ' / '),
                $promise->buyers->pluck('document_number')->filter()->implode(', '),
                $promise->buyers->pluck('phone_one')->filter()->implode(', '),
                (float) $promise->value,
                $entry['paid'],
                $entry['balance'],
                $quotas ? $standing->paidQuotas().' de '.$quotas : 'Sin cuotas',
                $standing->currentQuota ? $this->excelDate(CarbonImmutable::parse($standing->currentQuota['due_date'])) : null,
                $standing->currentQuota ? $standing->currentPending : null,
                $standing->overdueAmount,
                $standing->daysOverdue,
                $entry['status']->label(),
            ];
        })->all();
    }

    /**
     * @return array<string, string>
     */
    public function columnFormats(): array
    {
        return [
            'B' => self::MONEY, 'C' => self::DATE, 'D' => self::MONEY, 'J' => self::DATE,
            'N' => self::MONEY, 'O' => self::MONEY, 'P' => self::MONEY,
            'R' => self::DATE, 'S' => self::MONEY, 'T' => self::MONEY, 'U' => '0',
        ];
    }

    /**
     * @return array<string, int>
     */
    public function columnWidths(): array
    {
        return [
            'A' => 13, 'B' => 15, 'C' => 13, 'D' => 15, 'E' => 18, 'F' => 22, 'G' => 16, 'H' => 22,
            'I' => 42, 'J' => 13, 'K' => 42, 'L' => 22, 'M' => 22, 'N' => 17, 'O' => 17, 'P' => 17,
            'Q' => 15, 'R' => 15, 'S' => 15, 'T' => 15, 'U' => 9, 'V' => 16,
        ];
    }

    /**
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => fn (AfterSheet $event) => $this->decorate($event->sheet->getDelegate()),
        ];
    }

    private function decorate(Worksheet $sheet): void
    {
        $lastRow = self::FIRST_ROW + max(0, $this->statement->entries->count() - 1);

        $sheet->setCellValue('A1', 'Estado de cartera · '.$this->campaign.' · corte '.$this->statement->cutOff->format('d/m/Y')
            .($this->statement->onlyLate ? ' · solo promesas en mora' : ''));
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->getStyle('A2:V2')->applyFromArray(self::headerStyle());
        $sheet->getRowDimension(2)->setRowHeight(32);
        $sheet->freezePane('A3');

        if ($this->statement->entries->isEmpty()) {
            return;
        }

        $sheet->setAutoFilter('A2:V'.$lastRow);
        $sheet->getStyle('A3:V'.$lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        foreach (['A', 'E', 'F', 'I', 'K', 'L', 'M'] as $column) {
            $sheet->getStyle($column.'3:'.$column.$lastRow)->getAlignment()->setWrapText(true);
        }

        foreach ($this->statement->entries->values() as $index => $entry) {
            $sheet->getStyle('V'.(self::FIRST_ROW + $index))->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $entry['status']->fill()]],
            ]);
        }

        // SUBTOTAL leaves out the rows hidden by the filter
        $totalRow = $lastRow + 2;
        $sheet->setCellValue('M'.$totalRow, 'Total');
        foreach (['N', 'O', 'P', 'T'] as $column) {
            $sheet->setCellValue($column.$totalRow, '=SUBTOTAL(9,'.$column.self::FIRST_ROW.':'.$column.$lastRow.')');
        }
        $sheet->getStyle('M'.$totalRow.':V'.$totalRow)->applyFromArray(self::headerStyle());
        $sheet->getStyle('N'.$totalRow.':T'.$totalRow)->getNumberFormat()->setFormatCode(self::MONEY);
    }

    /**
     * Bold on the orange of the other reports, with thin borders.
     *
     * @return array<string, mixed>
     */
    public static function headerStyle(): array
    {
        return [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBBF77']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'B45309']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
    }

    private function excelDate(?CarbonInterface $date): float|int|null
    {
        return $date ? Date::dateTimeToExcel($date) : null;
    }

    private function clean(string $text): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Distinct non-empty values in one cell, or nothing when there are none.
     *
     * @param  Collection<int, mixed>  $values
     */
    private function join(Collection $values, string $separator = ', '): ?string
    {
        $text = $values->filter(fn ($value) => $value !== null && $value !== '')->unique()->implode($separator);

        return $text === '' ? null : $text;
    }
}
