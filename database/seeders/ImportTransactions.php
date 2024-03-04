<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Imports\DataImport;
use App\Models\Block;
use App\Models\Parcel;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportTransactions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->command->info('Importing transactions...');

        $collection = Excel::toCollection(new DataImport, public_path('imports/transacciones.xlsx'));


        try {

            DB::beginTransaction();

            foreach ($collection->first() as $row) {

                if ($row['recibo_no']) {
                    if ($row['mz'] && $row['lote']) {

                        $block = Block::where('code', 'like', '%' . strval($row['mz']) . '%')->first();

                        $parcel = Parcel::where('number', 'like', '%' . strval($row['lote']) . '%')->where('block_id', $block->id)->with('promise')->first();

                        if ($parcel) {

                            Payment::create([
                                'bill_number' => $row['recibo_no'],
                                'agreement_date' => $row['fecha'] ? Date::excelToDateTimeObject($row['fecha'])->format('Y-m-d') : null,
                                'agreement_amount' => $row['valor'],
                                'payment_date' => $row['fecha'] ? Date::excelToDateTimeObject($row['fecha'])->format('Y-m-d') : null,
                                'paid_amount' => $row['valor'],
                                'bank' => $row['banco'],
                                'payment_method' => $row['recibo_no'] == 'TRANSFERENCIA' ? PaymentMethod::BANK_TRANSFER : PaymentMethod::DEPOSIT,
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
            }


            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();

            $this->command->error($th->getMessage());
        }

        echo 'Done importing transactions';
    }
}
