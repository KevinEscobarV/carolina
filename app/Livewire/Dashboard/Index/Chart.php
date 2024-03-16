<?php

namespace App\Livewire\Dashboard\Index;

use App\Enums\Range;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Chart extends Component
{
    #[Reactive]
    public Filters $filters;

    public $dataset = [];

    public function fillDataset()
    {
        $increment = match ($this->filters->range) {
            Range::Today => DB::raw("DATE_FORMAT(payment_date, '%H') as increment"),
            Range::All_Time => DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as increment"),
            // Range::Year => DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as increment"),
            default => DB::raw("DATE(payment_date) as increment"),
        };

        $results = Payment::query()
            ->select($increment, DB::raw('SUM(paid_amount) as total'))
            ->tap(function ($query) {
                $this->filters->apply($query);
            })
            ->groupBy('increment')
            ->get();

        $this->dataset['values'] = $results->pluck('total')->toArray();
        $this->dataset['labels'] = $results->pluck('increment')->toArray();
    }

    public function placeholder()
    {
        return view('components.chart.placeholder');
    }

    public function render()
    {
        $this->fillDataset();
        return view('livewire.dashboard.index.chart');
    }
}
