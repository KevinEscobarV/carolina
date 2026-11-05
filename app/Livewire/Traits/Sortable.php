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

    /**
     * Columns the table may be sorted by. The column name comes from the browser, so anything else is ignored.
     *
     * @return array<int, string>
     */
    abstract protected function sortableColumns(): array;

    public function updatedPerPage($value)
    {
        $this->perPage = in_array((string) $value, ['5', '10', '15', '20', '25', '50', '100'], true) ? (string) $value : '10';
    }

    public function sortBy($column)
    {
        if (! in_array($column, $this->sortableColumns(), true)) {
            return;
        }

        if ($this->sortCol === $column) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }

    /**
     * The current sort column, or null when it is not sortable (it may also arrive through the URL).
     */
    protected function sortColumn(): ?string
    {
        return in_array($this->sortCol, $this->sortableColumns(), true) ? $this->sortCol : null;
    }
}
