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
        public string $fromDate,
        public string $toDate,
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Buyer::query()
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
        ];
    }

    public function columnFormats(): array
    {
        return [
        ];
    }

    public function headings(): array
    {
        return [
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

