<?php

namespace App\Livewire\Deed\Edit;

use App\Livewire\Forms\DeedForm;
use App\Models\Deed;
use Livewire\Component;
use WireUi\Traits\Actions;

class Page extends Component
{
    use Actions;

    public $block;
    
    public Deed $deed;

    public DeedForm $form;

    public function mount()
    {
        $this->form->setModel($this->deed);
        $this->block = $this->deed->parcel->block_id;
    }

    public function save()
    {
        $update = $this->form->update();

        if ($update) {
            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Escritura actualizada exitosamente',
                'description' => 'La escritura se ha actualizado correctamente',
                'accept'      => [
                    'label' => 'Volver a escrituras',
                    'url' => route('deeds'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.deed.edit.page');
    }
}
