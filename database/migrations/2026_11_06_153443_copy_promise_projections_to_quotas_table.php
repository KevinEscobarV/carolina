<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Copies each promise's projection (JSON column) into the quotas table. Dates and amounts are kept
 * as they are, in their order; only the balance is recalculated, because it was never updated after
 * the quotas were edited by hand. The JSON column stays as a backup until the quotas are verified.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('promises')
            ->whereNotNull('projection')
            ->orderBy('id')
            ->chunkById(100, function ($promises) {
                $now = now();
                $rows = [];

                foreach ($promises as $promise) {
                    $projection = json_decode((string) $promise->projection, true);

                    if (! is_array($projection) || $projection === []) {
                        continue;
                    }

                    $hasInterest = (float) $promise->interest_rate > 0;
                    $balance = round((float) $promise->value - (float) $promise->initial_fee);

                    foreach (array_values($projection) as $index => $quota) {
                        $amount = round((float) ($quota['payment_amount'] ?? 0));
                        $interest = $hasInterest ? round((float) ($quota['interest_payment'] ?? 0)) : 0;
                        $principal = $amount - $interest;
                        $balance -= $principal;

                        $rows[] = [
                            'promise_id' => $promise->id,
                            'number' => $index + 1,
                            'due_date' => substr((string) $quota['due_date'], 0, 10),
                            'amount' => $amount,
                            'interest' => $interest,
                            'principal' => $principal,
                            'balance' => $balance,
                            'is_extraordinary' => false,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('quotas')->insert($chunk);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('quotas')->delete();
    }
};
