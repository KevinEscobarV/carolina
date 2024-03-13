<?php

namespace App\Models;

use App\Enums\DeedStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deed extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'value',
        'signature_date',
        'book',
        'status',
        'observations',
        'parcel_id'
    ];

    protected $casts = [
        'signature_date' => 'date',
        'status' => DeedStatus::class
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    public function getValueFormattedAttribute(): string
    {
        return number_format($this->value, 0, ',', '.');
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
            $query->where('number', 'like', '%' . $search . '%')
                ->orWhere('book', 'like', '%' . $search . '%');
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
            if ($column == 'parcel') {
                $query->join('parcels', 'parcels.id', '=', 'deeds.parcel_id')
                    ->orderBy('parcels.number', $asc ? 'asc' : 'desc')
                    ->select('deeds.*');
            } else if ($column == 'block') {
                $query->join('parcels', 'parcels.id', '=', 'deeds.parcel_id')
                    ->join('blocks', 'blocks.id', '=', 'parcels.block_id')
                    ->orderBy('blocks.code', $asc ? 'asc' : 'desc')
                    ->select('deeds.*');
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
