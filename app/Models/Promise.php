<?php

namespace App\Models;

use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Number;

class Promise extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'signature_date',
        'value',
        'initial_fee',
        'number_of_fees',
        'interest_rate',
        'cut_off_date',
        'payment_frequency',
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
        'cut_off_date' => 'date',
        'payment_frequency' => PaymentFrequency::class,
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
    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    }

    public function getValueFormattedAttribute(): string
    {
        return Number::currency($this->value, 'COP');
    }

    public function getInitialFeeFormattedAttribute(): string
    {
        return Number::currency($this->initial_fee, 'COP');
    }

    public function getDeedValueFormattedAttribute(): string
    {
        return Number::currency($this->deed_value, 'COP');
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
        if ($search)
            $query->whereHas('buyers', function (Builder $query) use ($search) {
                $query->where('names', 'like', "%$search%")
                    ->orWhere('surnames', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('document_number', 'like', "%$search%");
            });
    }

        /**
     * Scope a query to sort buyers by the specified column.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  bool  $asc
     * @return void
     */
    public function scopeSort(Builder $query, string $column = null, bool $asc): void
    {
        if ($column) {
            if ($column === 'buyer') {
                $query->join('buyer_promise', 'buyer_promise.promise_id', '=', 'promises.id')
                    ->join('buyers', 'buyers.id', '=', 'buyer_promise.buyer_id')
                    ->orderBy('buyers.names', $asc ? 'asc' : 'desc');
            } else if ($column === 'parcel') {
                $query->join('parcels', 'parcels.promise_id', '=', 'promises.id')
                    ->orderBy('parcels.number', $asc ? 'asc' : 'desc');
            } else {
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            }
        }
    }
}
