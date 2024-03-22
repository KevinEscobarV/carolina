<?php

namespace App\Livewire\Dashboard\Index;

use App\Enums\Range;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Doughnut extends Component
{
    #[Reactive]
    public Filters $filters;

    public $dataset = [];

    public function fillDataset()
    {
        $results = Payment::query()
            ->select('payment_method', DB::raw('COUNT(*) as total'))
            ->tap(function ($query) {
                $this->filters->apply($query);
            })
            ->groupBy('payment_method')
            ->get();

        $this->dataset['values'] = $results->pluck('total')->toArray();
        $this->dataset['labels'] = $results->map(function ($result) {
            return $result->payment_method->label();
        })->toArray();
    }

    public function placeholder()
    {
        return view('components.doughnut.placeholder');
    }

    public function render()
    {
        $this->fillDataset();
        return view('livewire.dashboard.index.doughnut');
    }
}
