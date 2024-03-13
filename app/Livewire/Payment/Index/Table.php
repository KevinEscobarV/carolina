<?php

namespace App\Livewire\Payment\Index;

use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Payment;
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
        $this->model = new Payment(); // This is used for the SoftDeletes trait
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

    #[On('refresh-payment-table')]
    public function render()
    {
        return view('livewire.payment.index.table', [
            'payments' => Payment::search($this->search)->sort($this->sortCol, $this->sortAsc)->trash($this->trash)->with('promise.buyers', 'promise.parcels.block')->paginate($this->perPage),
        ]);
    }
}
