<?php

namespace App\Livewire\Buyers;

use App\Models\Buyer;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class BuyerComponent extends Component
{
    use WithPagination;

    public $search = '';

    public $sortCol;

    public $sortAsc = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortCol = $column;
    }

    #[Title('Compradores')]
    public function render()
    {
        sleep(1);

        return view('livewire.buyers.buyer-component', [
            'buyers' => Buyer::search($this->search)->sort($this->sortCol, $this->sortAsc)->paginate(10),
        ]);
    }
}
