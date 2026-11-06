<?php

namespace App\Livewire\Promise\Index;

use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Payment;
use App\Models\Promise;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
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

    public function mount()
    {
        $this->model = new Promise; // This is used for the SoftDeletes trait
    }

    #[Renderless]
    public function export()
    {
        return Promise::toCsv();
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
        return ['id', 'number', 'buyer', 'signature_date', 'signature_deed_date', 'value', 'initial_fee', 'quota_amount', 'interest_rate', 'payment_frequency', 'payment_method', 'status', 'observations'];
    }

    #[On('refresh-promise-table')]
    public function render()
    {
        return view('livewire.promise.index.table', [
            'promises' => Promise::search($this->search)->sort($this->sortColumn(), $this->sortAsc)->with('buyers', 'payments', 'quotas', 'parcels.block')->trash($this->trash)->paginate($this->perPage),
            'total_value' => number_format(Promise::sum('value'), 0, ',', '.'),
            'total_paid_amount' => number_format(Payment::sum('paid_amount'), 0, ',', '.'),
        ]);
    }
}
