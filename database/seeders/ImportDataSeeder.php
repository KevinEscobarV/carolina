<?php

namespace Database\Seeders;

use App\Enums\ParcelPosition;
use App\Imports\DataImport;
use App\Models\Block;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collection = Excel::toCollection(new DataImport, public_path('imports/lotes.xlsx'));
        $collection->first()->groupBy('manzana')->map(function ($item, $key) {
            Block::create([
                'code' => $key,
                'category_id' => 1,
            ])->parcels()->createMany($item->map(function ($item) {
                return [
                    'number' => $item['lote'],
                    'area_m2' => $item['area'],
                    'position' => $item['ubicacion'] === 'MEDIANERO' ? ParcelPosition::POSITION_MIDDLE : ParcelPosition::POSITION_CORNER,
                    'value' => $item['valor_lote'],
                ];
            })->toArray());
        });

        echo 'Done';
    }
}
