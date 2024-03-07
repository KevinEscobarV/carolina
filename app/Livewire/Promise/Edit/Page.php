<?php

namespace App\Livewire\Promise\Edit;

use App\Livewire\Forms\PromiseForm;
use App\Models\Promise;
use Livewire\Component;
use WireUi\Traits\Actions;

class Page extends Component
{    
    use Actions;

    public $block = null;
    
    public Promise $promise;

    public PromiseForm $form;

    public function mount()
    {
        $this->form->setModel($this->promise);
    }

    public function save()
    {
        $save = $this->form->update();

        if ($save) {
            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Promesa actualizada exitosamente',
                'description' => 'La promesa se ha actualizado correctamente',
                'accept'      => [
                    'label' => 'Volver a promesas',
                    'url' => route('promises'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }
    public function render()
    {
        return view('livewire.promise.edit.page');
    }
}
