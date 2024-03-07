<?php

namespace App\Http\Controllers\Api\Promises;

use App\Http\Controllers\Controller;
use App\Models\Promise;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Index extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Promise::query()
            ->select('id', 'number', 'status')
            ->with('buyers:id,names,surnames,document_number', 'parcels.block')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('number', 'like', "%{$request->search}%")
                    ->orWhereHas(
                        'buyers',
                        fn (Builder $query) => $query->where('names', 'like', "%{$request->search}%")
                            ->orWhere('document_number', 'like', "%{$request->search}%")
                    )
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(30)
            )
            ->get()
            ->map(function (Promise $promise) {
                $promise->description = 'Compradores: ' . $promise->buyers->pluck('names')->join(', ')
                    . '<br>Codigo Lotes: ' . $promise->parcels->groupBy('block_id')->map(function ($parcels) {
                        return $parcels->first()->block->code . ': (' . $parcels->pluck('number')->join(', ') . ')';
                    })->join(', ');
                return $promise;
            });
    }
}
