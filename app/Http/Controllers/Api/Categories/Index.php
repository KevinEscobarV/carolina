<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Index extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Category::query()
            ->select('id', 'name')
            ->with('blocks')
            ->orderBy('name')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('name', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(50)
            )
            ->get()->map(function (Category $category) {
                $category->description = 'Manzanas: ' . $category->blocks->count();
                return $category;
            });
    }
}
