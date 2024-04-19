<?php

namespace Database\Seeders;

use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Models\Payment;
use App\Models\Promise;
use App\Models\Scopes\CategoryScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePromises extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating promises...');

        $chunk = 1000;

        try {

            DB::beginTransaction();

            Promise::withoutGlobalScope(CategoryScope::class)
                ->where('projection', null)
                ->where('payment_method', PromisePaymentMethod::CREDIT)
                ->chunk($chunk, function ($promises) {
                    foreach ($promises as $promise) {

                        $numCuotas = $promise->quota_amount > 0
                            ? $promise->value / $promise->quota_amount
                            : 1;

                        $numCuotas = round($numCuotas);

                        $interes = $promise->interest_rate;
                        $valorTotal = $promise->value;

                        $fechaPago = $promise->cut_off_date;
                        $periodicidad = $promise->payment_frequency;
                
                        $cronograma = [];
                
                        // Calcular la tasa de interés según la periodicidad
                        $tasaInteres = $interes > 0
                            ? $interes / 100 / $periodicidad->multiplier()
                            : 0;
                
                        // Calcular el valor de la cuota
                        $valorCuota = $tasaInteres > 0
                            ? ($valorTotal * $tasaInteres) / (1 - pow((1 + $tasaInteres), -$numCuotas))
                            : $valorTotal / $numCuotas;
                
                        // Calcular el balance inicial
                        $balance = $valorTotal;
                
                        // Iterar sobre el número de cuotas
                        for ($cuota = 1; $cuota <= $numCuotas; $cuota++) {
                            // Calcular el pago de intereses
                            $pagoIntereses = $balance * $tasaInteres;
                
                            // Calcular el pago a capital
                            $pagoCapital = $valorCuota - $pagoIntereses;
                
                            // Calcular el nuevo balance
                            $balance -= $pagoCapital;
                
                            // Agregar detalles del pago al array
                            $cronograma[] = [
                                'number' => $cuota,
                                'due_date' => $fechaPago->format('Y-m-d'),
                                'payment_amount' => round($valorCuota, 2),
                                'interest_payment' => round($pagoIntereses, 2),
                                'principal_payment' => round($pagoCapital, 2),
                                'remaining_balance' => round($balance, 2),
                            ];
                
                            // Actualizar la fecha de corte
                            $fechaPago = match ($periodicidad) {
                                PaymentFrequency::WEEKLY => $fechaPago->addWeek(),
                                PaymentFrequency::BIWEEKLY => $fechaPago->addWeeks(2),
                                PaymentFrequency::MONTHLY => $fechaPago->addMonth(),
                                PaymentFrequency::QUARTERLY => $fechaPago->addMonths(3),
                                PaymentFrequency::SEMI_ANNUAL => $fechaPago->addMonths(6),
                                PaymentFrequency::ANNUAL => $fechaPago->addYear(),
                                PaymentFrequency::IRREGULAR => $fechaPago,
                            };
                        }
                
                        $promise->update([
                            'number_of_fees' => $numCuotas,
                            'projection' => $cronograma,
                        ]);
                    }
                });

            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();

            $this->command->error($th->getMessage());
        }

        echo 'Done updating promises';
    }
}
