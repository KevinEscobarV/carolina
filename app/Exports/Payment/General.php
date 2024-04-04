<?php

namespace App\Exports\Payment;

use App\Models\Payment;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class General implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function __construct(
        public $fromDate = null,
        public $toDate = null,
        public $paymentMethods = null,
    ) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Payment::query()->with('promise.buyers', 'promise.parcels.block')
            ->whereDate('payment_date', '>=', $this->fromDate)->whereDate('payment_date', '<=', $this->toDate)
            ->when($this->paymentMethods, fn ($query) => $query->whereIn('payment_method', $this->paymentMethods))
            ->orderBy('payment_date', 'asc');
        return $query;
    }

    /**
     * @param Payment $payment
     */
    public function map($payment): array
    {
        return [
            $payment->bill_number,
            $payment->agreement_date ? Date::dateTimeToExcel($payment->agreement_date) : 'Sin fecha',
            $payment->agreement_amount,
            $payment->payment_date ? Date::dateTimeToExcel($payment->payment_date) : 'Sin fecha',
            $payment->paid_amount,
            $payment->bank ?? 'No aplica',
            $payment->payment_method->label(),
            $payment->promise ? $payment->promise->number : 'Sin promesa',
            $payment->promise ? $payment->promise->buyers->map(fn ($buyer) => $buyer->document_number)->implode(', ') : 'N/A',
            $payment->promise ? $payment->promise->buyers->map(fn ($buyer) => $buyer->names . ' ' . $buyer->surnames)->implode(', ') : 'N/A',
            $payment->promise ? $payment->promise->parcels->map(fn ($parcel) => $parcel->block->code . ':' . $parcel->number)->implode(', ') : 'N/A',
            $payment->observations,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_ACCOUNTING_USD,
        ];
    }

    public function headings(): array
    {
        return [
            'N° recibo',
            'Fecha pactada',
            'Monto pactado',
            'Fecha de pago',
            'Monto pagado',
            'Banco',
            'Método de pago',
            'N° promesa',
            'N° documento',
            'Compradores',
            'Lotes',
            'Observaciones',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->setAutoFilter('A1:L1');

        return [
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'FF9C86',
                    ],
                    'endColor' => [
                        'argb' => 'FFC186',
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
            'C' => ['alignment' => ['horizontal' => 'right']],
            'D' => ['alignment' => ['horizontal' => 'right']],
            'E' => ['alignment' => ['horizontal' => 'right']],
            'I' => ['alignment' => ['horizontal' => 'right']],
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $highestRow = $event->sheet->getDelegate()->getHighestRow();

        $agreement_amount = '=SUM(C2:C' . $highestRow . ')';
        $paid_amount = '=SUM(E2:E' . $highestRow . ')';

        $event->sheet->setCellValue('C' . ($highestRow + 2), $agreement_amount);
        $event->sheet->setCellValue('E' . ($highestRow + 2), $paid_amount);

        $event->sheet->getStyle('A' . ($highestRow + 2) . ':E' . ($highestRow + 2))->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FF9C86',
                ],
                'endColor' => [
                    'argb' => 'FFC186',
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
