<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class Block extends Model
{
    use HasFactory, SoftDeletes;

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
     * Get the category that owns the block.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

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
        if ($search) {
            $query->where('code', 'like', '%' . $search . '%')
                ->orWhere('area_m2', 'like', '%' . $search . '%');
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
}
