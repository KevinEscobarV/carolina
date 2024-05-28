<?php

namespace App\Livewire\User\Index;

use App\Livewire\Forms\UserForm;
use App\Models\Category;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    
    public $roles = [];
    public $categories = [];

    public UserForm $form;

    public function mount()
    {
        $this->roles = Role::select('id', 'name')->get();
        $this->categories = Category::select('id', 'name')->get();
    }

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Usuario creado exitosamente',
                'El usuario se ha creado correctamente'
            );

            $this->dispatch('refresh-user-table');
        }
    }

    public function render()
    {
        return view('livewire.user.index.create');
    }
}
