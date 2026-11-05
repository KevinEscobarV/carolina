<?php

namespace App\Livewire\Payment\Index;

use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

#[Lazy]
class Table extends Component
{
    use Actions;
    use SoftDeletes;
    use Sortable;
    use WithPagination;

    public $model = null;

    public $promiseIds = [];

    public function mount()
    {
        $this->model = new Payment; // This is used for the SoftDeletes trait
        $this->sortCol = 'payment_date';
    }

    #[Renderless]
    public function export()
    {
        return Payment::toCsv();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    /**
     * @return array<int, string>
     */
    protected function sortableColumns(): array
    {
        return ['id', 'bill_number', 'agreement_date', 'agreement_amount', 'payment_date', 'paid_amount', 'payment_method', 'bank', 'observations'];
    }

    public function render()
    {
        $total = Payment::select(DB::raw('SUM(agreement_amount) as total_agreement_amount'), DB::raw('SUM(paid_amount) as total_paid_amount'))->promiseFilter($this->promiseIds)->trash($this->trash)->first();

        return view('livewire.payment.index.table', [
            'payments' => Payment::search($this->search)->sort($this->sortColumn(), $this->sortAsc)->promiseFilter($this->promiseIds)->trash($this->trash)->with('promise.buyers', 'promise.parcels.block')->paginate($this->perPage),
            'total_agreement_amount' => number_format($total->total_agreement_amount, 0, ',', '.'),
            'total_paid_amount' => number_format($total->total_paid_amount, 0, ',', '.'),
        ]);
    }
}
