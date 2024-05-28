<?php

namespace App\Livewire\Permission\Index;

use App\Livewire\Forms\RoleForm;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    
    public $permissions = [];

    public RoleForm $form;

    public function mount()
    {
        $this->permissions = Permission::select('id', 'name', 'description')->get();
    }

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Rol creado exitosamente',
                'El rol se ha creado correctamente'
            );

            $this->dispatch('refresh-role-table');
        }
    }

    public function render()
    {
        return view('livewire.permission.index.create');
    }
}
