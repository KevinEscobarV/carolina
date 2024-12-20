<?php

namespace App\Models;

use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class Block extends Model
{
    use HasFactory, SoftDeletes, HasCategory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'area',
        'area_m2',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'area' => Polygon::class,
    ];

    /**
     * Get the parcels for the block.
     */
    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    }

    /**
     * Scope a query to only include buyers that match the search.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return void
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $operator = config('database.operator');
        if ($search) {
            $query->where('code', $operator, '%' . $search . '%')
                ->orWhere('area_m2', $operator, '%' . $search . '%');
        }
    }

    /**
     * Scope a query to only include buyers that match the search.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return void
     */
    public function scopeSort(Builder $query, string $column = null, bool $asc): void
    {
        if ($column) {
            if ($column === 'category') {
                $query->join('categories', 'blocks.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $asc ? 'asc' : 'desc')
                    ->select('blocks.*');
            } else {
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            }
        }
    }
        
    /**
     * Scope a query to only include buyers that are trashed.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $onlyTrash
     * @return void
     */
    public function scopeTrash(Builder $query, bool $onlyTrash): void
    {
        if ($onlyTrash) $query->onlyTrashed();
    }
}
