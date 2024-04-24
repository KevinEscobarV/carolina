<?php

namespace Database\Seeders;

use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
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

        $promises = Promise::withoutGlobalScope(CategoryScope::class)
            ->where('created_at', '<', '2024-04-11 00:00:00')
            ->where('payment_method', PromisePaymentMethod::CREDIT)
            ->get();

        foreach ($promises as $promise) {
            try {
                $this->command->info("Updating promise {$promise->id}");

                $numCuotas = $promise->quota_amount > 0
                    ? $promise->value / $promise->quota_amount
                    : 1;

                $numCuotas = round($numCuotas);
                $numCuotas = $numCuotas < 1 ? 1 : $numCuotas;

                if ($numCuotas > 100) {
                    $this->command->error("Promise {$promise->id} has more than 100 quotas");
                    continue;
                }

                $interes = $promise->interest_rate;
                $valorTotal = $promise->value;

                $fechaPago = $promise->signature_date;
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
                    'cut_off_date' => $promise->signature_date,
                    'number_of_fees' => $numCuotas,
                    'projection' => $cronograma,
                ]);

                $this->command->warn("Promise {$promise->id} updated");

            } catch (\Throwable $th) {
                $this->command->error($th->getMessage());
                continue;
            }
        }



        echo 'Done updating promises';
    }
}
