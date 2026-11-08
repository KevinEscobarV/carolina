<?php

namespace Tests\Feature\Livewire\Reports\Promise;

use App\Enums\PaymentMethod;
use App\Exports\Promise\WalletStatus as WalletStatusExport;
use App\Livewire\Reports\Promise\WalletStatus;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class WalletStatusTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-20');
        $this->signInToCampaign()->assignRole(Role::create(['name' => 'Administrador']));
    }

    public function test_downloads_the_portfolio_on_the_cut_off_date(): void
    {
        Excel::fake();

        Livewire::test(WalletStatus::class)
            ->assertSet('cutOffDate', '2026-04-20')
            ->set('cutOffDate', '2026-03-31')
            ->set('onlyLate', true)
            ->call('exportGeneral');

        Excel::assertDownloaded('Estado-de-cartera-rivarca-2026-03-31.xlsx', fn (WalletStatusExport $export) => $export->cutOff->toDateString() === '2026-03-31' && $export->onlyLate);
    }

    public function test_rejects_a_cut_off_date_in_the_future(): void
    {
        Livewire::test(WalletStatus::class)
            ->set('cutOffDate', '2026-04-21')
            ->call('exportGeneral')
            ->assertHasErrors('cutOffDate');
    }

    public function test_the_workbook_starts_with_the_columns_of_the_administration_and_adds_a_summary(): void
    {
        $promise = $this->promiseWithQuotas('PROMESA 1');
        $this->pay($promise, 100_000, '2026-01-15')->update(['bill_number' => 'R-1']);
        // The last payment day had one receipt per quota
        $this->pay($promise, 100_000, '2026-02-15')->update(['bill_number' => 'R-2', 'observations' => 'PAGO  CUOTA FEBRERO']);
        $this->pay($promise, 100_000, '2026-02-15')->update(['bill_number' => 'R-3', 'observations' => 'PAGO CUOTA MARZO', 'payment_method' => PaymentMethod::BANK_TRANSFER, 'bank' => 'Banco de Bogotá']);

        $path = tempnam(sys_get_temp_dir(), 'cartera');
        file_put_contents($path, Excel::raw(new WalletStatusExport(CarbonImmutable::today(), campaign: 'Rivarca'), ExcelFormat::XLSX));
        $book = IOFactory::load($path);
        unlink($path);

        $this->assertSame(['Estado de cartera', 'Resumen'], $book->getSheetNames());

        $detail = $book->getSheet(0);
        $this->assertSame(
            ['N° recibo', 'Monto pactado', 'Fecha de pago', 'Monto pagado', 'Banco', 'Método de pago', 'N° promesa', 'Lotes', 'Compradores', 'Fecha pactada', 'Observaciones'],
            $detail->rangeToArray('A2:K2')[0],
        );
        $this->assertSame(
            ['R-2, R-3', 200_000.0, 200_000.0, 'No aplica, Banco de Bogotá', 'Efectivo, Transferencia bancaria', 'PROMESA 1', 'PAGO CUOTA FEBRERO / PAGO CUOTA MARZO'],
            array_map(fn (string $cell) => $detail->getCell($cell)->getValue(), ['A3', 'B3', 'D3', 'E3', 'F3', 'G3', 'K3']),
        );
        $this->assertSame(['3 de 6', 100_000, 5, '1 a 30 días'], array_map(fn (string $cell) => $detail->getCell($cell)->getValue(), ['Q3', 'T3', 'U3', 'V3']));
        $this->assertSame('=SUBTOTAL(9,P3:P3)', $detail->getCell('P5')->getValue());

        $summary = $book->getSheet(1);
        $this->assertSame(['1 a 30 días', 1, 300_000, 100_000], $summary->rangeToArray('A7:D7', null, false, false)[0]);
    }
}
