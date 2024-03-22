<?php

namespace App\Livewire\Parcel\Index;

use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Parcel;
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
        $this->model = new Parcel(); // This is used for the SoftDeletes trait
    }

    #[Renderless]
    public function export()
    {
        return Parcel::toCsv();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-parcel-table')] 
    public function render()
    {
        return view('livewire.parcel.index.table', [
            'parcels' => Parcel::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('block.category', 'promise')->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
