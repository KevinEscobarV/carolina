<?php

namespace App\Imports\Deed;

use App\Enums\DeedStatus;
use App\Models\Deed;
use App\Models\Parcel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class General implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    /**
     * @param array $row
     *
     * @return Deed|null
     */
    public function model(array $row)
    {
        $parcel = Parcel::where('number', $row['lote'])->whereHas('block', function ($query) use ($row) {
            $query->where('code', $row['manzana']);
        })->with('block')->first();

        $status = match ($row['estado']) {
            'PENDIENTE' => DeedStatus::PENDING,
            'REALIZADA' => DeedStatus::PAID,
            'CANCELADA' => DeedStatus::CANCELLED,
            default => DeedStatus::PAID,
        };

        if (!$parcel) {
            return null;
        }

        return new Deed([
            'parcel_id' => $parcel->id,
            'number' => $row['numero_escritura'],
            'signature_date' => $row['fecha'] ? Date::excelToDateTimeObject($row['fecha']) : null,
            'value' => $row['valor'],
            'book' => $row['libro_opcional'],
            'status' => $status,
            'observations' => $row['observaciones'],
         ]);
    }

    public function rules(): array
    {
        return [
            'lote' => 'required',
            'manzana' => 'required',
            'numero_escritura' => 'required',
            'fecha' => 'nullable',
            'valor' => 'required',
            'libro_opcional' => 'nullable',
            'estado' => 'required',
        ];
    }
}
