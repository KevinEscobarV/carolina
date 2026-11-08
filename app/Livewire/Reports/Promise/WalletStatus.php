<?php

namespace App\Livewire\Reports\Promise;

use App\Exports\Promise\WalletStatus as PromiseWalletStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use WireUi\Traits\Actions;

class WalletStatus extends Component
{
    use Actions;

    public ?string $cutOffDate = null;

    public bool $onlyLate = false;

    public function mount(): void
    {
        $this->cutOffDate = today()->toDateString();
    }

    public function exportGeneral(): ?BinaryFileResponse
    {
        $this->authorize('view.promises');

        $this->validate([
            'cutOffDate' => 'required|date|before_or_equal:today',
            'onlyLate' => 'boolean',
        ],
            [],
            [
                'cutOffDate' => 'fecha de corte',
                'onlyLate' => 'solo promesas en mora',
            ]);

        try {
            $cutOff = CarbonImmutable::parse($this->cutOffDate)->startOfDay();
            $campaign = (string) auth()->user()->currentCategory?->name;
            $name = 'Estado-de-cartera-'.Str::slug($campaign ?: 'campana').'-'.$cutOff->format('Y-m-d').'.xlsx';

            $this->notification()->success('Reporte generado', 'El reporte se ha exportado correctamente');

            return (new PromiseWalletStatus($cutOff, $this->onlyLate, $campaign))->download($name);
        } catch (\Throwable $th) {
            $this->notification()->error(
                'Error al exportar el reporte',
                'Ocurrió un error al exportar el reporte, por favor intente nuevamente, error: '.$th->getMessage()
            );

            return null;
        }
    }

    public function render(): View
    {
        return view('livewire.reports.promise.wallet-status');
    }
}
