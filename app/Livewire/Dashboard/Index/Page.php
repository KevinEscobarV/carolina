<?php

namespace App\Livewire\Dashboard\Index;

use App\Models\Payment;
use App\Models\Promise;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Panel administrativo')]
class Page extends Component
{
    public Filters $filters;

    public $projection;
    public $total;

    public function mount()
    {
        $this->filters->init();
        $this->projection = number_format(Promise::sum('value'), 0, ',', '.');
        $this->total = number_format(Payment::sum('paid_amount'), 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.dashboard.index.page');
    }
}
