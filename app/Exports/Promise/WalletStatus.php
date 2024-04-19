<?php

namespace App\Exports\Promise;

use App\Models\Promise;
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
    ) {
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Promise::query()->with('buyers', 'parcels.block', 'category', 'payments')
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
            'N' => NumberFormat::FORMAT_ACCOUNTING_USD,
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
            'Monto pagado',
            'Lotes',
            'Campaña',
            'Observaciones',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->setAutoFilter('A1:Q1');

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
            'O' => ['alignment' => ['horizontal' => 'right']],

        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $highestRow = $event->sheet->getDelegate()->getHighestRow();

        $agreement_amount = '=SUM(F2:F' . $highestRow . ')';
        $paid_amount = '=SUM(N2:N' . $highestRow . ')';

        $event->sheet->setCellValue('E' . ($highestRow + 2), 'Total');
        $event->sheet->setCellValue('F' . ($highestRow + 2), $agreement_amount);
        $event->sheet->setCellValue('N' . ($highestRow + 2), $paid_amount);

        $event->sheet->getStyle('A' . ($highestRow + 2) . ':Q' . ($highestRow + 2))->applyFromArray([
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
