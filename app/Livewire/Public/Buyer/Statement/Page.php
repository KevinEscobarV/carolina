<?php

namespace App\Livewire\Public\Buyer\Statement;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Account statement page; the search itself lives in the Lookup component, also used on the homepage.
 */
class Page extends Component
{
    #[Layout('layouts.public')]
    #[Title('Estado de cuenta')]
    public function render(): View
    {
        return view('livewire.public.buyer.statement.page');
    }
}
