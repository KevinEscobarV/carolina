<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Scopes\CategoryScope;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateBuyersPhone extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating buyers phone...');

        $chunk = 100;

        try {

            DB::beginTransaction();

            Buyer::withoutGlobalScope(CategoryScope::class)
                ->whereNotNull('phone_one')
                ->chunk($chunk, function ($buyers) {
                    foreach ($buyers as $buyer) {

                        $buyer->phone_one = '57' . $buyer->phone_one;
                        if ($buyer->phone_two) {
                            $buyer->phone_two = '57' . $buyer->phone_two;
                        }

                        $buyer->save();
                    }
                });

            DB::commit();
            
        } catch (\Throwable $th) {

            DB::rollBack();

            $this->command->error($th->getMessage());
        }

        $this->command->info('Buyers phone updated successfully');
    }
}
