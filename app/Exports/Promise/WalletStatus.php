<?php

namespace App\Exports\Promise;

use App\Models\Promise;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WalletStatus implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function __construct(
        public $fromDate = null,
        public $toDate = null,
        public $onlyLate = false,
    ) {
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $raw = config('database.default') === 'pgsql'
        ? "((projection->>(((SELECT COUNT(*) FROM payments WHERE payments.promise_id = promises.id and payments.is_initial_fee = '0') + 1)::int))::jsonb->>'due_date')::date < CURRENT_DATE"
        : "JSON_UNQUOTE(JSON_EXTRACT(`projection`, CONCAT('$[', ((SELECT COUNT(*) FROM `payments` WHERE `payments`.`promise_id` = `promises`.`id` AND `payments`.`is_initial_fee` = '0') + 1), '].due_date'))) < CURDATE()";

        $query = Promise::query()->with('buyers', 'parcels.block', 'category', 'payments')
            ->when($this->onlyLate, fn ($query) => $query->whereRaw($raw))
            ->when($this->fromDate, fn ($query) => $query->whereDate('signature_date', '>=', $this->fromDate))
            ->when($this->toDate, fn ($query) => $query->whereDate('signature_date', '<=', $this->toDate))
            ->orderBy('signature_date', 'asc');
        return $query;
    }

    /**
     * @param Promise $promise
     */
    public function map($promise): array
    {
        $mora = 0;
        $current_quota = null;

        if (isset($promise->current_quota['due_date'])) {
            $mora = Carbon::parse($promise->current_quota['due_date'])->diffInDays(Carbon::now()->format('Y-m-d'));
            $mora = $mora > 0 ? $mora : 0;
        }

        if (isset($promise->current_quota['due_date'])) {
            $current_quota = Date::dateTimeToExcel(Carbon::parse($promise->current_quota['due_date']));
        }
        

        return [
            $promise->number,
            $promise->buyers ? $promise->buyers->map(fn ($buyer) => $buyer->document_number)->implode(', ') : 'N/A',
            $promise->buyers ? $promise->buyers->map(fn ($buyer) => $buyer->names . ' ' . $buyer->surnames)->implode(', ') : 'N/A',
            $promise->status->text(),
            $promise->signature_date ? Date::dateTimeToExcel($promise->signature_date) : 'Sin fecha',
            $promise->value,
            $promise->initial_fee,
            $promise->quota_amount,
            $promise->interest_rate,
            $promise->number_of_fees,
            $promise->number_of_paid_fees,
            $promise->payment_frequency->label(),
            $promise->payment_method->label(),
            $current_quota ? $current_quota : 'Sin fecha',
            $mora,
            $promise->total_paid,
            $promise->parcels->map(fn ($parcel) => $parcel->block->code . ':' . $parcel->number)->implode(', '),
            $promise->category ? $promise->category->name : 'Sin campaña',
            $promise->observations, 
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'G' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'H' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'I' => NumberFormat::FORMAT_PERCENTAGE,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'O' => NumberFormat::FORMAT_NUMBER,
            'P' => NumberFormat::FORMAT_ACCOUNTING_USD,
        ];
    }

    public function headings(): array
    {
        return [
            'N° promesa',
            'Documento',
            'Nombres',
            'Estado',
            'Fecha de firma',
            'Monto pactado',
            'Cuota inicial',
            'Monto cuota',
            'Tasa de interés',
            'N° cuotas',
            'N° cuotas pagadas',
            'Frecuencia de pago',
            'Método de pago',
            'Cuota actual',
            'Días en mora',
            'Monto pagado',
            'Lotes',
            'Campaña',
            'Observaciones',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->setAutoFilter('A1:S1');

        return [
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'F97316',
                    ],
                    'endColor' => [
                        'argb' => 'FCA76C',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => ['vertical' => 'center'],
            ],

            'A' => ['alignment' => ['horizontal' => 'right']],
            'B' => ['alignment' => ['horizontal' => 'right']],
            'E' => ['alignment' => ['horizontal' => 'right']],
            'F' => ['alignment' => ['horizontal' => 'right']],
            'G' => ['alignment' => ['horizontal' => 'right']],
            'H' => ['alignment' => ['horizontal' => 'right']],
            'I' => ['alignment' => ['horizontal' => 'right']],
            'J' => ['alignment' => ['horizontal' => 'right']],
            'K' => ['alignment' => ['horizontal' => 'right']],
            'N' => ['alignment' => ['horizontal' => 'right']],
            'O' => ['alignment' => ['horizontal' => 'right']],

        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $highestRow = $event->sheet->getDelegate()->getHighestRow();

        $agreement_amount = '=SUM(F2:F' . $highestRow . ')';
        $paid_amount = '=SUM(P2:P' . $highestRow . ')';

        $event->sheet->setCellValue('E' . ($highestRow + 2), 'Total');
        $event->sheet->setCellValue('F' . ($highestRow + 2), $agreement_amount);
        $event->sheet->setCellValue('P' . ($highestRow + 2), $paid_amount);

        $event->sheet->getStyle('A' . ($highestRow + 2) . ':S' . ($highestRow + 2))->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'F97316',
                ],
                'endColor' => [
                    'argb' => 'FCA76C',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => ['vertical' => 'center'],
            'numberFormat' => ['formatCode' => NumberFormat::FORMAT_ACCOUNTING_USD],
        ]);
    }
}
