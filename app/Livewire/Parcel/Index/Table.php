<?php

namespace App\Livewire\Parcel\Index;

use App\Models\Parcel;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $sortCol;

    #[Url]
    public $sortAsc = false;

    public $selectedParcelIds = [];

    public $parcelIdsOnPage = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }

    #[Renderless]
    public function export()
    {
        return Parcel::toCsv();
    }

    public function deleteSelected()
    {
        $parcels = Parcel::whereIn('id', $this->selectedParcelIds)->get();

        foreach ($parcels as $parcel) {
            $this->archive($parcel);
        }
    }

    public function archive(Parcel $parcel)
    {
        $parcel->delete();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-parcel-table')] 
    public function render()
    {
        return view('livewire.parcel.index.table', [
            'parcels' => Parcel::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('block.category', 'promise')->paginate(10),
        ]);
    }
}
