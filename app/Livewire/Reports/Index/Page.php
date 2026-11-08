<?php

namespace App\Livewire\Reports\Index;

use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * The Excel reports in one place; each one shows only to those who can see its data.
 */
#[Title('Reportes')]
class Page extends Component
{
    /**
     * Permissions that give access to at least one report.
     */
    public const PERMISSIONS = ['view.promises', 'view.buyers', 'view.parcels', 'view.payments'];

    public function mount(): void
    {
        abort_unless(auth()->user()->canAny(self::PERMISSIONS), 403);
    }

    public function render(): View
    {
        return view('livewire.reports.index.page');
    }
}
