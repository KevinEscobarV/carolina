<?php

namespace App\Models;

use App\Enums\DeedStatus;
use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deed extends Model
{
    use HasFactory, SoftDeletes, HasCategory;

    protected $fillable = [
        'number',
        'value',
        'signature_date',
        'book',
        'status',
        'observations',
        'promise_id',
        'category_id'
    ];

    protected $casts = [
        'signature_date' => 'date',
        'status' => DeedStatus::class
    ];

    public function promise()
    {
        return $this->belongsTo(Promise::class);
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
                ->orWhere('book', 'like', '%' . $search . '%')
                ->orWhereHas('promise', function ($query) use ($search) {
                    $query->whereHas('buyers', function ($query) use ($search) {
                        $query->where('names', 'like', '%' . $search . '%');
                    });
                });
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
            $query->orderBy($column, $asc ? 'asc' : 'desc');
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
