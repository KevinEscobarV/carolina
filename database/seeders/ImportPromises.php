<?php

namespace Database\Seeders;

use App\Enums\CivilStatus;
use App\Enums\DeedStatus;
use App\Enums\DocumentType;
use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Imports\DataImport;
use App\Models\Buyer;
use App\Models\Deed;
use App\Models\Parcel;
use App\Models\Promise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportPromises extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collection = Excel::toCollection(new DataImport, public_path('imports/promesas.xlsx'));

        try {

            DB::beginTransaction();

            foreach ($collection->first() as $item) {

                $parcel = Parcel::where('number', $item['lote'])->whereHas('block', function ($query) use ($item) {
                    $query->where('code', $item['manzana']);
                })->first();

                if (is_numeric($item['no_de_folio_de_matricula'])) {
                    Deed::create([
                        'number' => $item['no_de_folio_de_matricula'],
                        'value' => $item['gastos_de_escrituracion_y_registro'] ? $item['gastos_de_escrituracion_y_registro'] : 0,
                        'signature_date' => $item['fecha_de_firma_de_escritura'] ? Date::excelToDateTimeObject($item['fecha_de_firma_de_escritura'])->format('Y-m-d') : now()->format('Y-m-d'),
                        'status' => DeedStatus::PAID,
                        'observations' => $item['observaciones'],
                        'parcel_id' => $parcel->id,
                    ]);
                } else if (is_string($item['no_de_folio_de_matricula'])) {
                    Deed::create([
                        'number' => null,
                        'value' => $item['gastos_de_escrituracion_y_registro'] ? $item['gastos_de_escrituracion_y_registro'] : 0,
                        'signature_date' => $item['fecha_de_firma_de_escritura'] ? Date::excelToDateTimeObject($item['fecha_de_firma_de_escritura'])->format('Y-m-d') : now()->format('Y-m-d'),
                        'status' => DeedStatus::PENDING,
                        'observations' => $item['observaciones'],
                        'parcel_id' => $parcel->id,
                    ]);
                }

                if ($item['cedula']) {
                    $buyer = Buyer::firstOrCreate([
                        'document_number' => $item['cedula'],
                    ], [
                        'names' => $item['nombres'],
                        'surnames' => $item['apellidos'] . ' ' . $item['segundo_apellido'],
                        'email' => $item['email'],
                        'document_type' => DocumentType::CC,
                        'civil_status' => $this->processCivilStatus($item['estado_civil']),
                        'phone_one' => $item['celular_1'],
                        'phone_two' => $item['celular_2'],
                        'address' => $item['direccion_actual'],
                    ]);
                } else {
                    $buyer = null;
                }

                if ($buyer) {

                    $promise = Promise::where('number', $item['consecutivo_promesa'])->first();

                    if (!$promise) {

                        $periodicity = $this->processPeriodicity($item['fechas'], $item['fecha_de_firma_de_escritura']);

                        $promise = Promise::create([
                            'number' => $item['consecutivo_promesa'],
                            'signature_date' => $item['fecha_de_firma_de_promesa'] ? Date::excelToDateTimeObject($item['fecha_de_firma_de_promesa'])->format('Y-m-d') : now()->format('Y-m-d'),
                            'value' => $item['valor_lote'] ? $item['valor_lote'] : 0,
                            'initial_fee' => $item['cuota_inicial'] ? $item['cuota_inicial'] : 0,
                            'cut_off_date' => $periodicity['cut_off_date'],
                            'payment_frequency' => $periodicity['payment_frequency'],
                            'payment_method' => $item['forma_de_pago'] == 'FINANCIADO' ? PromisePaymentMethod::CREDIT : PromisePaymentMethod::CASH,
                            'observations' => $item['observaciones'],
                            'status' => $item['fecha_de_firma_de_promesa'] ? PromiseStatus::CONFIRMED : PromiseStatus::PENDING,
                        ]);
                    } else {
                        $value = $item['valor_lote'] ? $item['valor_lote'] : 0;
                        $promise->value = $value + $promise->value;
                        $promise->save();
                    }
                    
                    $promise->buyers()->syncWithoutDetaching($buyer->id);
                    $parcel->value = $item['valor_lote'] ? $item['valor_lote'] : 0;
                    $parcel->promise_id = $promise->id;
                    $parcel->save();
                }
            };

            DB::commit();

        } catch (\Throwable $th) {

            DB::rollBack();

            $this->command->error($th->getMessage());
        }

        echo 'Done';
    }

    public function processCivilStatus($status)
    {
        switch ($status) {
            case 'CASADO':
                return CivilStatus::MARRIED;
            case 'CASADOS':
                return CivilStatus::MARRIED;
            case 'CASADA':
                return CivilStatus::MARRIED;
            case 'UNION LIBRE':
                return CivilStatus::FREE_UNION;
            case 'UNIÓN LIBRE':
                return CivilStatus::FREE_UNION;
            case 'DIVORCIADO':
                return CivilStatus::DIVORCED;
            case 'VIUDO':
                return CivilStatus::WIDOWER;
            default:
                return CivilStatus::SINGLE;
        }
    }

    public function processPeriodicity($value, $date)
    {
        // Value like a '2 DE CADA MES', '03 DE CADA MES', etc
        $periodicity = explode(' ', $value);

        $date = Date::excelToDateTimeObject($date);

        if (is_numeric($periodicity[0])) {
            $cut_off_date = $date->format('Y-m') . '-' . $periodicity[0];
            $payment_frequency = PaymentFrequency::MONTHLY;
        } else {
            $cut_off_date = $date->format('Y-m-d');
            $payment_frequency = PaymentFrequency::IRREGULAR;
        }

        return [
            'cut_off_date' => $cut_off_date,
            'payment_frequency' => $payment_frequency,
        ];
    }
}
