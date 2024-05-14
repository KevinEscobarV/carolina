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
        public $position = null,
        public $status = null,
        public $registrationNumber = null,
    ) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Parcel::query()
            ->when($this->registrationNumber, fn ($query) => $this->registrationNumber == 'withRegistrationNumber' ? $query->whereNotNull('registration_number') : $query->whereNull('registration_number'))
            ->when($this->position, fn ($query) => $query->where('position', $this->position))
            ->when($this->status, fn ($query) => $this->status == 'available' ? $query->whereNull('promise_id') : $query->whereNotNull('promise_id'))
            ->with('block', 'category', 'promise.deed')
            ->orderBy('parcels.block_id', 'asc');

        return $query;
    }

    /**
     * @param Parcel $parcel
     */
    public function map($parcel): array
    {
        $deed = $parcel->promise ? $parcel->promise->deed : null;

        return [
            $parcel->category ? $parcel->category->name : 'Sin campaña',
            $parcel->block->code,
            $parcel->number,
            $parcel->position->label(),
            $parcel->area_m2,
            $parcel->value,
            $parcel->promise ? $parcel->promise->number : null,
            $parcel->promise ? $parcel->promise->value : 0,
            $parcel->promise ? Date::dateTimeToExcel($parcel->promise->signature_date) : null,
            $parcel->registration_number,
            $deed ? $deed->number : null,
            $deed ? $deed->value : 0,
            $deed ? Date::dateTimeToExcel($deed->signature_date) : null,
            $parcel->promise ? $parcel->promise->status->text() : 'Disponible',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'H' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_ACCOUNTING_USD,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
            'Promesa',
            'Valor promesa',
            'Fecha promesa',
            'Folio de Matrícula',
            'Escritura',
            'Valor escritura',
            'Fecha escritura',
            'Estado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->setAutoFilter('A1:N1');

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

        $parcels_value = '=SUM(F2:F' . $highestRow . ')';

        $event->sheet->setCellValue('E' . ($highestRow + 2), 'Total');
        $event->sheet->setCellValue('F' . ($highestRow + 2), $parcels_value);
        

        $event->sheet->getStyle('A' . ($highestRow + 2) . ':N' . ($highestRow + 2))->applyFromArray([
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
