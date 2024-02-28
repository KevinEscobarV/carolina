<?php

namespace App\Livewire\Buyer\Index;

use App\Models\Buyer;
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

    public $selectedUserIds = [];

    public $userIdsOnPage = [];

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
        return Buyer::toCsv();
    }

    public function deleteSelected()
    {
        $buyers = Buyer::whereIn('id', $this->selectedUserIds)->get();

        foreach ($buyers as $buyer) {
            $this->archive($buyer);
        }
    }

    public function archive(Buyer $buyer)
    {
        $buyer->delete();
    }

    #[On('refresh-buyer-table')] 
    public function render()
    {
        return view('livewire.buyer.index.table', [
            'buyers' => Buyer::search($this->search)->sort($this->sortCol, $this->sortAsc)->paginate(8),
        ]);
    }

    public function placeholder()
    {
        return view('livewire.buyer.index.table-placeholder');
    }
}
