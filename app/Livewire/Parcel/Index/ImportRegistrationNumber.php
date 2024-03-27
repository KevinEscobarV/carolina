<?php

namespace App\Livewire\Parcel\Index;

use App\Imports\DataImport;
use App\Models\Parcel;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use WireUi\Traits\Actions;

class ImportRegistrationNumber extends Component
{
    use Actions;
    use WithFileUploads;

    public $openImport = false;
    public string $importErrors = 'Errores en la importación: <br>';

    #[Validate('required|file|mimes:xlsx', as: 'archivo')]
    public $file;

    public function import()
    {
        $this->validate();

        $collection = Excel::toCollection(new DataImport, $this->file);

        // Verificar primero si tiene el encabezado correcto
        $header = $collection->first()->first();

        if (!isset($header['lote']) || !isset($header['manzana']) || !isset($header['numero_matricula'])) {
            $this->dialog()->error(
                'Error !!!',
                'El archivo no tiene el formato correcto, por favor descargue la plantilla e intente de nuevo.'
            );

            $this->reset();

            return;
        }

        try {
            DB::beginTransaction();

            $fila = 1;

            foreach ($collection->first() as $row) {

                $fila++;

                if (!isset($row['lote']) || !isset($row['manzana']) || !isset($row['numero_matricula'])) {
                    $this->importErrors .= 'Fila ' . $fila . ': No tiene todos los campos requeridos. <br>';
                    continue;
                }

                $parcel = Parcel::where('number', trim($row['lote']))->whereHas('block', function ($query) use ($row) {
                    $query->where('code', trim($row['manzana']));
                })->first();

                if ($parcel) {
                    $parcel->update([
                        'registration_number' => trim($row['numero_matricula'])
                    ]);
                } else {
                    $this->importErrors .= 'Fila ' . $fila . ': No se encontró el lote y manzana. <br>';
                }
            }

            DB::commit();

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
            DB::rollBack();

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
            public_path('templates/Plantilla_Importación_Matriculas.xlsx')
        );
    }

    public function render()
    {
        return view('livewire.parcel.index.import-registration-number');
    }
}
