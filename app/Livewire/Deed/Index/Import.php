<?php

namespace App\Livewire\Deed\Index;

use App\Imports\Deed\General;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Import extends Component
{
    use WithFileUploads;

    public $openImport = false;

    public $importErrors = [];

    #[Validate('required|file|mimes:xlsx', as: 'archivo')]
    public $file;

    public function import()
    {
        $this->authorize('create.deeds');

        $this->validate();

        $import = new General;
        $import->import($this->file);

        $errors = [];

        foreach ($import->failures() as $failure) {
            $errors[] = $failure->toArray();
        }

        $this->importErrors = $errors;
    }

    public function downloadTemplate()
    {
        return response()->download(
            public_path('templates/Plantilla Importación Escrituras.xlsx')
        );
    }

    public function render()
    {
        return view('livewire.deed.index.import');
    }
}
