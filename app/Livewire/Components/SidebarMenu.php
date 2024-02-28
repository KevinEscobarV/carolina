<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class SidebarMenu extends Component
{
    #[Renderless]
    public function toggleSidebar()
    {
        Auth::user()->toggleSidebar();
    }

    public function render()
    {
        return view('livewire.components.sidebar-menu');
    }
}
