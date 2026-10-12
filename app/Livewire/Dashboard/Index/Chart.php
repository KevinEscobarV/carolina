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
        $driver = config('database.default');

        if ($driver === 'pgsql') {
            $today = DB::raw("DATE_TRUNC('hour', payment_date) as increment");
            $allTime = DB::raw("DATE_TRUNC('month', payment_date) as increment");
        } else if ($driver === 'mysql') {
            $today = DB::raw("DATE_FORMAT(payment_date, '%H') as increment");
            $allTime = DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as increment");
        } else if ($driver === 'sqlite') {
            $today = DB::raw("strftime('%H', payment_date) as increment");
            $allTime = DB::raw("strftime('%Y-%m', payment_date) as increment");
        } else {
            $today = DB::raw("DATE_FORMAT(payment_date, '%H') as increment");
            $allTime = DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as increment");
        }

        $increment = match ($this->filters->range) {
            Range::Today => $today,

            Range::All_Time => $allTime,

            // Range::Year => config('database.default') === 'pgsql'
            //     ? DB::raw("to_char(payment_date, 'YYYY-MM') as increment")
            //     : DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as increment"),

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
