<?php

namespace App\Livewire\Deed\Index;

use App\Models\Deed;
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

    public $selectedDeedIds = [];

    public $deedIdsOnPage = [];

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
        return Deed::toCsv();
    }

    public function deleteSelected()
    {
        $deeds = Deed::whereIn('id', $this->selectedDeedIds)->get();

        foreach ($deeds as $deed) {
            $this->archive($deed);
        }
    }

    public function archive(Deed $deed)
    {
        $deed->delete();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }


    #[On('refresh-deed-table')] 
    public function render()
    {
        return view('livewire.deed.index.table', [
            'deeds' => Deed::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('parcel')->paginate(8),
        ]);
    }
}
