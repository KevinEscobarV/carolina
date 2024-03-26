<?php

namespace App\Livewire\Deed\Index;

use App\Imports\Deed\General;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class Import extends Component
{
    use WithFileUploads;

    public $openImport = false;
    public $importErrors = [];

    #[Validate('required|file|mimes:xlsx', as: 'archivo')]
    public $file;

    public function import()
    {
        $this->validate();

        $import = new General();
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
