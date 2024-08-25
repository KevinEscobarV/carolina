<?php

namespace App\Livewire\Parcel\Index;

use App\Imports\Parcel\General;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;

class Import extends Component
{
    use Actions;
    use WithFileUploads;

    public $openImport = false;
    public $update_existing = false;
    public string $importErrors = 'Errores en la importación: <br>';

    #[Validate('required|file|mimes:xlsx', as: 'archivo')]
    public $file;

    public function import()
    {
        try {
            $this->validate();

            $import = new General($this->update_existing);
            $import->import($this->file);

            foreach ($import->failures() as $failure) {
                foreach ($failure->errors() as $error) {
                    $this->importErrors .= 'Fila ' . $failure->row() . ': ' . $error . '<br>';
                }
            }

            if ($this->importErrors != 'Errores en la importación: <br>') {
                $this->dialog([
                    'title'       => 'La importación se realizó con errores !!!',
                    'description' => $this->importErrors,
                    'icon'        => 'warning',
                    'style'       => 'inline',
                ]);
            } else {
                $this->dialog()->success(
                    'Importación Exitosa !!!',
                    'Los datos se importaron correctamente.'
                );
            }

            $this->reset();

            $this->dispatch('refresh-parcel-table');
        } catch (\Throwable $th) {

            $this->reset();

            $this->dialog()->error(
                'Error !!!',
                'Ocurrió un error al importar los datos, por favor intente de nuevo: ' . $th->getMessage()
            );
        }
    }

    public function downloadTemplate()
    {
        return response()->download(
            public_path('templates/Plantilla_Importación_Lotes.xlsx')
        );
    }

    public function render()
    {
        return view('livewire.parcel.index.import');
    }
}
