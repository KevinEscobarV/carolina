<?php

namespace App\Livewire\Deed\Index;

use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Deed;
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
        $this->model = new Deed(); // This is used for the SoftDeletes trait
    }

    #[Renderless]
    public function export()
    {
        return Deed::toCsv();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }


    #[On('refresh-deed-table')] 
    public function render()
    {
        return view('livewire.deed.index.table', [
            'deeds' => Deed::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('parcel')->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
