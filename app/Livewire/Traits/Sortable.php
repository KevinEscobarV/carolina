<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\Url;

trait Sortable
{
    #[Url]
    public $search = '';

    #[Url]
    public $sortCol;

    #[Url]
    public $sortAsc = false;

    public $perPage = '10';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage($value)
    {
        $this->perPage = $value ?? '10';
    }

    public function sortBy($column)
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }
}
