<?php

namespace App\Livewire\Block\Index;

use App\Models\Block;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    #[Url('seachBlock')]
    public $search = '';

    #[Url('sortColBlock')]
    public $sortCol;

    #[Url('sortAscBlock')]
    public $sortAsc = false;

    public $selectedBlockIds = [];

    public $BlockIdsOnPage = [];

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
        return Block::toCsv();
    }

    public function deleteSelected()
    {
        $Blocks = Block::whereIn('id', $this->selectedBlockIds)->get();

        foreach ($Blocks as $Block) {
            $this->archive($Block);
        }
    }

    public function archive(Block $Block)
    {
        $Block->delete();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-block-table')] 
    public function render()
    {
        return view('livewire.block.index.table', [
            'blocks' => Block::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('category')->paginate(10, ['*'], 'blocks'),
        ]);
    }
}
