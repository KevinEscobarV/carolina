<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Imports\DataImport;
use App\Models\Parcel;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportPayments extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collection = Excel::toCollection(new DataImport, public_path('imports/pagos.xlsx'));

        $collection->first()->map(function ($row) {
            if ($row['recibo_no']) {
                if ($row['mz'] && $row['lote']) {
                    $parcel = Parcel::where('number', $row['lote'])->with('promise')->whereHas('block', function ($query) use ($row) {
                        $query->where('code', $row['mz']);
                    })->first();
        
                    if ($parcel) {

                        Payment::create([
                            'bill_number' => $row['recibo_no'],
                            'agreement_date' => $row['fecha'] ? Date::excelToDateTimeObject($row['fecha'])->format('Y-m-d') : null,
                            'agreement_amount' => $row['valor'],
                            'payment_date' => $row['fecha'] ? Date::excelToDateTimeObject($row['fecha'])->format('Y-m-d') : null,
                            'paid_amount' => $row['valor'],
                            'payment_method' => PaymentMethod::CASH,
                            'observations' => $row['concepto'],
                            'promise_id' => $parcel->promise->id,
                        ]);

                    } else {
                        $this->command->info('Parcel not found: ' . $row['mz'] . ' - ' . $row['lote'] . ' Recibo:' . $row['recibo_no']);
                    }

                } else {
                    $this->command->info('Parcel not defined: ' . $row['mz'] . ' - ' . $row['lote'] . ' Recibo:' . $row['recibo_no']);
                }
            }
        });
    }
}
