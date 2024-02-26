<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'block_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'location' => Point::class,
        'area' => Polygon::class,
    ];

    const POSITION_CORNER = 'corner';
    const POSITION_MIDDLE = 'middle';

    const POSITIONS = [
        [
            'value' => self::POSITION_CORNER,
            'label' => 'Esquinero',
        ],
        [
            'value' => self::POSITION_MIDDLE,
            'label' => 'Medianero',
        ]
    ];

    /**
     * Get the position label.
     * 
     * @return string
     */
    public function getPositionLabelAttribute(): string
    {
        return collect(self::POSITIONS)->firstWhere('value', $this->position)['label'] ?? 'N/A';
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
    public function promise(): HasOne
    {
        return $this->hasOne(Promise::class);
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
