<?php

namespace App\Http\Controllers\Api\Parcels;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Index extends Controller
{
    public function promises(Block $block, Request $request): Collection
    {
        return Parcel::query()
            ->select('id', 'number', 'value', 'area_m2', 'promise_id', 'block_id', 'category_id')
            ->orderBy('number')
            ->with('block')
            ->when(
                $block->exists,
                fn (Builder $query) => $query->where('block_id', $block->id),
            )
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('number', config('database.operator'), "%{$request->search}%"),
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
            )
            ->get()->map(function (Parcel $parcel) {

                $status = $parcel->promise_id ? 'En Promesa' : 'Disponible';
                $led = $parcel->promise_id ? '🟡' : '🟢';

                $parcel->number = $parcel->number . ' - ' . $parcel->block->code . ' ' . $led;

                $parcel->description = 'Manzana: ' . $parcel->block->code
                    . '<br>Area: ' . number_format($parcel->area_m2) . ' m²'
                    . '<br>Valor: $' . number_format($parcel->value, 0, ',', '.')
                    . '<br>Estado: ' . $status;

                $parcel->disabled = $parcel->promise_id ? true : false;

                return $parcel;
            });
    }

    public function deeds(Block $block, Request $request): Collection
    {
        return $block->parcels()->select('id', 'number', 'value', 'area_m2')
            ->with('deed')
            ->orderBy('number')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('number', config('database.operator'), "%{$request->search}%"),
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
            )
            ->get()->map(function (Parcel $parcel) use ($block) {

                $status = $parcel->deed ? 'Escriturado' : 'Sin Escriturar';
                $led = $parcel->deed ? '🔵' : '🟢';

                $parcel->number = $parcel->number . ' - ' . $block->code . ' ' . $led;

                $parcel->description = 'Manzana: ' . $block->code
                    . '<br>Area: ' . number_format($parcel->area_m2) . ' m²'
                    . '<br>Valor: $' . number_format($parcel->value, 0, ',', '.')
                    . '<br>Estado: ' . $status;

                $parcel->disabled = $parcel->deed ? true : false;

                return $parcel;
            });
    }
}
