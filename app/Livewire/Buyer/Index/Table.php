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
    use SoftDeletes;
    use Sortable;
    use WithPagination;

    public $model = null;

    public function mount()
    {
        $this->model = new Buyer; // This is used for the SoftDeletes trait
        $this->sortCol = 'names';
        $this->sortAsc = true;
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

    /**
     * @return array<int, string>
     */
    protected function sortableColumns(): array
    {
        return ['id', 'names', 'surnames', 'email', 'document_type', 'document_number', 'civil_status'];
    }

    #[On('refresh-buyer-table')]
    public function render()
    {
        return view('livewire.buyer.index.table', [
            'buyers' => Buyer::search($this->search)->sort($this->sortColumn(), $this->sortAsc)->with('promises.parcels.block', 'promises.payments')->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
