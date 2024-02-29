<?php

namespace App\Models;

use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promise extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parcel_id',
        'promise',
        'signature_date',
        'value',
        'initial_fee',
        'number_of_fees',
        'interest_rate',
        'deed_value',
        'deed_number',
        'deed_date',
        'payment_method',
        'observations',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'signature_date' => 'date',
        'deed_date' => 'date',
        'payment_method' => PromisePaymentMethod::class,
        'status' => PromiseStatus::class,
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the buyer that owns the promise.
     */
    public function buyers(): BelongsToMany
    {
        return $this->belongsToMany(Buyer::class);
    }

    /**
     * Get the parcel that owns the promise.
     */
    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }
}
