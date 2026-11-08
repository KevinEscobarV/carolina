<?php

namespace App\Livewire\Dashboard\Index;

use App\Services\Dashboard\Overview;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Panel administrativo')]
class Page extends Component
{
    public function render(): View
    {
        $overview = new Overview;

        return view('livewire.dashboard.index.page', [
            'campaign' => auth()->user()->currentCategory?->name,
            'totals' => $overview->totals(),
            'monthly' => $overview->monthly(),
            'aging' => $overview->aging(),
            'methods' => $overview->paymentMethods(),
            'sales' => $overview->sales(),
            'upcoming' => $overview->upcoming(),
        ]);
    }
}
