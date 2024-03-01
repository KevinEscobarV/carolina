<?php

namespace App\Http\Controllers\Api\Blocks;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Index extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Block::query()
            ->select('id', 'code', 'area_m2', 'category_id')
            ->orderBy('code')
            ->when(
                $request->search,
                fn (Builder $query) => $query->where('code', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(50)
            )
            ->with('category:id,name')
            ->get()->map(function (Block $block) {
                $block->description = 'Campaña: ' . $block->category->name 
                    . '<br>Área: ' . number_format($block->area_m2, 2, ',', '.') . ' m2';
                return $block;
            });
    }
}
