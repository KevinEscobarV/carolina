<?php

namespace App\Exports\Buyer;

use App\Models\Buyer;
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

class General implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents
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
        $query = Buyer::query()->with('promises.payments')
            ->whereDate('created_at', '>=', $this->fromDate)->whereDate('created_at', '<=', $this->toDate)
            ->orderBy('created_at', 'asc');
        return $query;
    }

    /**
     * @param Buyer $buyer
     */
    public function map($buyer): array
    {
        return [
            $buyer->names,
            $buyer->surnames,
            $buyer->email,
            $buyer->document_type->label(),
            $buyer->document_number,
            $buyer->civil_status->label(),
            $buyer->phone_one,
            $buyer->phone_two,
            $buyer->address,
            $buyer->category->name,
            $buyer->promises->count(),
            $buyer->promises ? $buyer->promises->map(fn ($promise) => $promise->number)->implode(', ') : 'Sin promesas',
            $buyer->promises->sum('value'),
            $buyer->promises->sum('total_paid'),
            $buyer->last_payment ? Date::dateTimeToExcel($buyer->last_payment->payment_date) : 'Sin fecha',
            $buyer->last_payment ? $buyer->last_payment->paid_amount : 0,
            $buyer->created_at ? Date::dateTimeToExcel($buyer->created_at) : 'Sin fecha',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'N' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'P' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'Q' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function headings(): array
    {
        return [
            'Nombres',
            'Apellidos',
            'Correo',
            'Tipo de documento',
            'Número de documento',
            'Estado civil',
            'Teléfono 1',
            'Teléfono 2',
            'Dirección',
            'Campaña',
            'Promesas',
            'Números de promesas',
            'Valor total de promesas',
            'Valor total pagado',
            'Última fecha de pago',
            'Último monto pagado',
            'Fecha de creación',
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
                        'argb' => 'EC4899',
                    ],
                    'endColor' => [
                        'argb' => 'FFA1CF',
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
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $highestRow = $event->sheet->getDelegate()->getHighestRow();

        $agreement_amount = '=SUM(M2:M' . $highestRow . ')';
        $paid_amount = '=SUM(N2:N' . $highestRow . ')';

        $event->sheet->setCellValue('L' . ($highestRow + 2), 'Total');
        $event->sheet->setCellValue('M' . ($highestRow + 2), $agreement_amount);
        $event->sheet->setCellValue('N' . ($highestRow + 2), $paid_amount);

        $event->sheet->getStyle('A' . ($highestRow + 2) . ':Q' . ($highestRow + 2))->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'EC4899',
                ],
                'endColor' => [
                    'argb' => 'FFA1CF',
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

