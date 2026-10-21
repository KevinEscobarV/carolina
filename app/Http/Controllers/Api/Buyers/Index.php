<?php

namespace App\Http\Controllers\Api\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Scopes\CategoryScope;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;


class Index extends Controller
{
    public function __invoke(Request $request): Collection
    {
        $operator = config('database.operator');

        return Buyer::query()
            ->select('id', 'names', 'surnames', 'document_type', 'document_number', 'email', 'category_id')
            ->orderBy('names')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('names', $operator, "%{$request->search}%")
                    ->orWhere('surnames', $operator, "%{$request->search}%")
                    ->orWhere('document_number', $operator, "%{$request->search}%")
                    ->orWhere('email', $operator, "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(30)
            )
            ->get()->map(function (Buyer $buyer) {
                $buyer->names = $buyer->names . ' ' . $buyer->surnames;
                $buyer->description = 'Documento: ' . strtoupper($buyer->document_type->value) . '. ' . $buyer->document_number
                    . '<br>Correo: ' . $buyer->email;
                return $buyer;
            });
    }

    /**
     * Buyers of other categories whose document is not registered yet in the current one.
     */
    public function otherCategories(Request $request): Collection
    {
        $operator = config('database.operator');
        $categoryId = $request->user()->currentCategory->id;

        return Buyer::withoutGlobalScope(CategoryScope::class)
            ->select('id', 'names', 'surnames', 'document_type', 'document_number', 'email', 'category_id')
            ->with('category:id,name')
            ->whereHas('category')
            ->where('category_id', '!=', $categoryId)
            ->whereNotIn(
                'document_number',
                fn (QueryBuilder $query) => $query->select('document_number')
                    ->from('buyers')
                    ->where('category_id', $categoryId)
                    ->whereNotNull('document_number')
            )
            ->orderBy('names')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('names', $operator, "%{$request->search}%")
                        ->orWhere('surnames', $operator, "%{$request->search}%")
                        ->orWhere('document_number', $operator, "%{$request->search}%")
                        ->orWhere('email', $operator, "%{$request->search}%")
                )
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(30)
            )
            ->get()->map(function (Buyer $buyer) {
                $buyer->names = $buyer->names . ' ' . $buyer->surnames;
                $buyer->description = 'Campaña: ' . $buyer->category->name
                    . '<br>Documento: ' . strtoupper($buyer->document_type?->value ?? '') . '. ' . $buyer->document_number;
                return $buyer;
            });
    }
}
