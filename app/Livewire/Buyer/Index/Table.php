<?php

namespace App\Livewire\Buyer\Index;

use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Buyer;
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
        $this->model = new Buyer(); // This is used for the SoftDeletes trait
    }

    #[Renderless]
    public function export()
    {
        return Buyer::toCsv();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-buyer-table')] 
    public function render()
    {
        return view('livewire.buyer.index.table', [
            'buyers' => Buyer::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('promises.parcels.block')->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
