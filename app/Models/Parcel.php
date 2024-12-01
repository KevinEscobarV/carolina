<?php

namespace App\Models;

use App\Enums\ParcelPosition;
use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Parcel extends Model
{
    use HasFactory, SoftDeletes, HasCategory, HasSpatial;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'position',
        'location',
        'area',
        'area_m2',
        'registration_number',
        'value',
        'block_id',
        'promise_id',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => ParcelPosition::class,
        'location' => Point::class,
        'area' => Polygon::class,
    ];

    public function getValueFormattedAttribute(): string
    {
        return number_format($this->value, 0, ',', '.');
    }

    /**
     * Get the block that owns the parcel.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the promise that owns the parcel.
     */
    public function promise(): BelongsTo
    {
        return $this->belongsTo(Promise::class);
    }

    /**
     * Get the buyer that owns the parcel.
     */
    public function owner(): HasOneThrough
    {
        return $this->hasOneThrough(Buyer::class, Promise::class);
    }

    /**
     * Get the payments for the parcel.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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
            $query->where('number', config('database.operator'), '%' . $search . '%')
                ->orWhereHas('block', function (Builder $query) use ($search) {
                    $query->where('code', config('database.operator'), '%' . $search . '%');
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
            if ($column == 'block') {
                $query->join('blocks', 'parcels.block_id', '=', 'blocks.id')
                    ->orderBy('blocks.code', $asc ? 'asc' : 'desc')
                    ->select('parcels.*');
            } else if ($column == 'promise') {
                $query->join('promises', 'parcels.promise_id', '=', 'promises.id')
                    ->orderBy('promises.deed_number', $asc ? 'asc' : 'desc')
                    ->select('parcels.*');
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
