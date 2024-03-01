<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\Url;

trait Sortable
{
    #[Url]
    public $sortCol;

    #[Url]
    public $sortAsc = false;

    public function sortBy($column)
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }
}
