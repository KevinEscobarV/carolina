<?php

namespace App\Livewire\Promise\Index;

use App\Models\Promise;
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

    public $selectedPromiseIds = [];

    public $promiseIdsOnPage = [];

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
        return Promise::toCsv();
    }

    public function deleteSelected()
    {
        $promises = Promise::whereIn('id', $this->selectedPromiseIds)->get();

        foreach ($promises as $promise) {
            $this->archive($promise);
        }
    }

    public function archive(Promise $promise)
    {
        $promise->delete();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-promise-table')] 
    public function render()
    {
        return view('livewire.promise.index.table', [
            'promises' => Promise::search($this->search)->sort($this->sortCol, $this->sortAsc)->paginate(8),
        ]);
    }
}
