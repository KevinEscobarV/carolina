<?php

namespace App\Livewire\Dashboard\Index;

use App\Enums\Range;
use App\Models\Payment;
use Livewire\Form;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

class Filters extends Form
{
    #[Url]
    public Range $range = Range::Year;

    #[Url]
    public $start;

    #[Url]
    public $end;

    #[Url]
    public FilterStatus $status = FilterStatus::All;

    public function init()
    {
        $this->initRange();
    }

    public function initRange()
    {
        if ($this->range !== Range::Custom) {
            $this->start = null;
            $this->end = null;
        }
    }

    public function statuses()
    {
        return collect(FilterStatus::cases())->map(function ($status) {
            $count = $this->applyRange(
                    $this->applyStatus(
                        Payment::query(),
                        $status,
                    )
                )->count();

            return [
                'value' => $status->value,
                'label' => $status->label(),
                'count' => $count,
            ];
        });
    }

    public function apply($query)
    {
       $query = $this->applyStatus($query);
       $query = $this->applyRange($query);

       return $query;
    }

    public function applyStatus($query, $status = null)
    {
        $status = $status ?? $this->status;

        if ($status === FilterStatus::All) {
            return $query;
        }

        return $query->where('payment_method', $status);
    }

    public function applyRange($query)
    {
        if ($this->range === Range::All_Time) {
            return $query;
        }

        if ($this->range === Range::Custom) {
            $start = Carbon::createFromFormat('Y-m-d', $this->start);
            $end = Carbon::createFromFormat('Y-m-d', $this->end);

            return $query->whereBetween('payment_date', [$start, $end]);
        }

        return $query->whereBetween('payment_date', $this->range->dates());
    }
}
