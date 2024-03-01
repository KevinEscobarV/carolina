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
    public function __invoke(Block $block, Request $request): Collection
    {
        return $block->parcels()->select('id', 'number', 'area_m2')
            ->orderBy('number')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('number', 'like', "%{$request->search}%"),
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(50)
            )
            ->get()->map(function (Parcel $parcel) use ($block) {
                $parcel->number = $parcel->number . ' - ' . $block->code;
                $parcel->description = 'Manzana: ' . $block->code
                    . '<br>Area: ' . number_format($parcel->area_m2) . ' m²';
                return $parcel;
            });
    }
}
