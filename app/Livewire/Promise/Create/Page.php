<?php

namespace App\Livewire\Promise\Create;

use App\Livewire\Forms\PromiseForm;
use App\Models\Parcel;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Crear promesa')]
class Page extends Component
{
    use Actions;
    public PromiseForm $form;

    public $block;

    public $switch_quota = 1;

    public $isCreate = true;

    public function updatedFormParcels($parcels): void
    {
        $this->form->value = Parcel::whereIn('id', $parcels)->sum('value');
    }

    public function mount()
    {
        $this->form->signature_date = now();
    }

    public function project(): void
    {
        $this->form->amortization();
    }

    public function save(): void
    {
        $this->form->switch_quota = $this->switch_quota;
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Promesa creada exitosamente',
                'La promesa se ha creado correctamente'
            );

            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Promesa creada exitosamente',
                'description' => 'La promesa se ha creado correctamente',
                'accept'      => [
                    'label' => 'Volver a tabla de promesas',
                    'url' => route('promises'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.promise.create.page');
    }
}
