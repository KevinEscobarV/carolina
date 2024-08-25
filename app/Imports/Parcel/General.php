<?php

namespace App\Imports\Parcel;

use App\Models\Block;
use App\Models\Parcel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class General implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function __construct(
        public $update_existing = false,
    ) {}

    /**
     * @param array $row
     *
     * @return Deed|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $parcel = Parcel::where('number', trim($row['lote']))->whereHas('block', function ($query) use ($row) {
                $query->where('code', trim($row['manzana']));
            })->first();

            if ($parcel) {
                if ($this->update_existing) {
                    $parcel->update([
                        'position' => $row['posicion'] == 'Esquinero' ? 'corner' : 'middle',
                        'registration_number' => $row['numero_matricula'] ?? $parcel->registration_number,
                        'area_m2' => $row['area_m2'],
                        'value' => $row['valor'],
                    ]);
                }
            } else {
                $block = Block::firstOrCreate([
                    'code' => $row['manzana'],
                ]);

                $block->parcels()->create([
                    'number' => $row['lote'],
                    'position' => $row['posicion'] == 'Esquinero' ? 'corner' : 'middle',
                    'registration_number' => $row['numero_matricula'] ?? null,
                    'area_m2' => $row['area_m2'],
                    'value' => $row['valor'],
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'lote' => 'required',
            'posicion' => 'required|in:Esquinero,Medianero',
            'manzana' => 'required',
            'numero_matricula' => 'nullable',
            'valor' => 'required',
            'area_m2' => 'required',
        ];
    }
}
