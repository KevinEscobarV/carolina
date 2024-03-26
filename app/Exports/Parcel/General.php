<?php

namespace App\Exports\Parcel;

use App\Models\Parcel;
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
        public string $position,
        public string $status
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Parcel::query()->with('block', 'category', 'promise', 'deed')
            ->when($this->position, fn ($query) => $query->where('position', $this->position))
            ->when($this->status, fn ($query) => $this->status === 'available' ? $query->whereHas('promise') : $query->whereDoesntHave('promise'))
            ->orderBy('created_at', 'asc');
        return $query;
    }

    /**
     * @param Parcel $parcel
     */
    public function map($parcel): array
    {
        return [
            $parcel->category ? $parcel->category->name : 'Sin campaña',
            $parcel->block->code,
            $parcel->number,
            $parcel->position->label(),
            $parcel->area_m2,
            $parcel->value,
            $parcel->deed ? $parcel->deed->number : null,
            $parcel->deed ? $parcel->deed->value : 0,
            $parcel->deed ? Date::dateTimeToExcel($parcel->deed->signature_date) : null,
            $parcel->promise ? $parcel->promise->number : null,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'H' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function headings(): array
    {
        return [
            'Campaña',
            'Manzana',
            'Lote',
            'Posición',
            'Área (m2)',
            'Valor',
            'Escritura',
            'Valor escritura',
            'Fecha escritura',
            'Promesa',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->setAutoFilter('A1:J1');

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

        $agreement_amount = '=SUM(F2:F' . $highestRow . ')';
        $paid_amount = '=SUM(H2:H' . $highestRow . ')';

        $event->sheet->setCellValue('E' . ($highestRow + 2), 'Total');
        $event->sheet->setCellValue('F' . ($highestRow + 2), $agreement_amount);
        $event->sheet->setCellValue('H' . ($highestRow + 2), $paid_amount);

        $event->sheet->getStyle('A' . ($highestRow + 2) . ':J' . ($highestRow + 2))->applyFromArray([
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
