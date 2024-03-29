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
    use WithPagination;
    use SoftDeletes;
    use Sortable;

    public $model = null;

    public function mount()
    {
        $this->model = new Promise(); // This is used for the SoftDeletes trait
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

    #[On('refresh-promise-table')] 
    public function render()
    {
        return view('livewire.promise.index.table', [
            'promises' => Promise::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('buyers', 'payments', 'parcels.block')->trash($this->trash)->paginate($this->perPage),
            'total_value' => number_format(Promise::sum('value'), 0, ',', '.'),
            'total_paid_amount' => number_format(Payment::sum('paid_amount'), 0, ',', '.'),
        ]);
    }
}
