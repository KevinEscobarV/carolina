<?php

namespace App\Http\Controllers\Api\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;


class Index extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Buyer::query()
            ->select('id', 'names', 'surnames', 'document_number', 'email')
            ->orderBy('names')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('surnames', 'like', "%{$request->search}%")
                    ->orWhere('document_number', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(30)
            )
            ->get();
    }
}
