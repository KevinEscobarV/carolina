<?php

namespace App\Exports\Promise;

use App\Services\Reports\PortfolioStatement;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Promises, balance and arrears of the portfolio per status, to present it at a glance.
 */
class WalletStatusSummary implements FromArray, WithColumnWidths, WithEvents, WithStrictNullComparison, WithTitle
{
    private const HEADER_ROW = 5;

    public function __construct(
        private readonly PortfolioStatement $statement,
        private readonly string $campaign,
    ) {}

    public function title(): string
    {
        return 'Resumen';
    }

    /**
     * @return list<list<mixed>>
     */
    public function array(): array
    {
        $summary = $this->statement->summary();
        $balance = max(1, array_sum(array_column($summary, 'balance')));

        $rows = [
            ['Resumen de cartera'],
            ['Campaña', $this->campaign],
            ['Fecha de corte', $this->statement->cutOff->format('d/m/Y').($this->statement->onlyLate ? ' · solo promesas en mora' : '')],
            [''],
            ['Estado', 'Promesas', 'Saldo por cobrar', 'Valor en mora', '% del saldo'],
        ];

        foreach ($summary as $group) {
            $rows[] = [$group['status']->label(), $group['count'], $group['balance'], $group['overdue'], $group['balance'] / $balance];
        }

        $rows[] = [
            'Total',
            array_sum(array_column($summary, 'count')),
            array_sum(array_column($summary, 'balance')),
            array_sum(array_column($summary, 'overdue')),
            array_sum(array_column($summary, 'balance')) > 0 ? 1 : 0,
        ];
        $rows[] = [''];
        $rows[] = ['Revisar saldo: todas las cuotas están cubiertas pero queda saldo (cuota inicial sin pagar o proyección descuadrada).'];
        $rows[] = ['Sin proyección: la promesa no tiene cuotas y todavía debe dinero.'];
        $rows[] = ['Último pago: si ese día hubo varios recibos, salen juntos en la misma fila y sus montos se suman.'];

        return $rows;
    }

    /**
     * @return array<string, int>
     */
    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 12, 'C' => 20, 'D' => 20, 'E' => 12];
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
        $summary = $this->statement->summary();
        $firstRow = self::HEADER_ROW + 1;
        $totalRow = $firstRow + count($summary);

        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setBold(true);
        $sheet->getStyle('A'.self::HEADER_ROW.':E'.self::HEADER_ROW)->applyFromArray(WalletStatusDetail::headerStyle());
        $sheet->getStyle('A'.$totalRow.':E'.$totalRow)->applyFromArray(WalletStatusDetail::headerStyle());

        foreach ($summary as $index => $group) {
            $sheet->getStyle('A'.($firstRow + $index))->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($group['status']->fill());
        }

        $sheet->getStyle('C'.$firstRow.':D'.$totalRow)->getNumberFormat()->setFormatCode(WalletStatusDetail::MONEY);
        $sheet->getStyle('E'.$firstRow.':E'.$totalRow)->getNumberFormat()->setFormatCode('0.0%');
        foreach (range($totalRow + 2, $totalRow + 4) as $noteRow) {
            $sheet->mergeCells('A'.$noteRow.':E'.$noteRow);
            $sheet->getStyle('A'.$noteRow)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($noteRow)->setRowHeight(30);
        }
        $sheet->getStyle('A'.($totalRow + 2).':A'.($totalRow + 4))->getFont()->setItalic(true)->getColor()->setRGB('6B7280');
    }
}
