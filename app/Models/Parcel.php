<?php

namespace App\Models;

use App\Enums\ParcelPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class Parcel extends Model
{
    use HasFactory;

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
        'value',
        'block_id',
        'promise_id',
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
}
