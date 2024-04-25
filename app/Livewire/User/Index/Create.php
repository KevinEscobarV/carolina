<?php

namespace App\Livewire\User\Index;

use App\Livewire\Forms\UserForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public UserForm $form;

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
