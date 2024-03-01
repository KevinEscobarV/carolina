<?php

namespace App\Livewire\Category\Index;

use App\Models\Category;
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

    public $selectedCategoryIds = [];

    public $categoryIdsOnPage = [];

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
        return Category::toCsv();
    }

    public function deleteSelected()
    {
        $categories = Category::whereIn('id', $this->selectedCategoryIds)->get();

        foreach ($categories as $category) {
            $this->archive($category);
        }
    }

    public function archive(Category $category)
    {
        $category->delete();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-category-table')] 
    public function render()
    {
        return view('livewire.category.index.table', [
            'categories' => Category::search($this->search)->sort($this->sortCol, $this->sortAsc)->paginate(8),
        ]);
    }
}
