<?php

namespace App\Livewire\Buyer\Edit;

use App\Livewire\Forms\BuyerForm;
use App\Models\Buyer;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Editar cliente')]
class Page extends Component
{
    use Actions;
    
    public Buyer $buyer;

    public BuyerForm $form;

    public function mount()
    {
        $this->form->setBuyer($this->buyer);
    }

    public function save()
    {
        $update = $this->form->update();

        if ($update) {
            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Cliente actualizado exitosamente',
                'description' => 'El cliente ha sido actualizado correctamente',
                'accept'      => [
                    'label' => 'Volver a clientes',
                    'url' => route('buyers'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.buyer.edit.page');
    }
}
