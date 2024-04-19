<?php

namespace App\Models;

use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Models\Traits\HasCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promise extends Model
{
    use HasFactory, SoftDeletes, HasCategory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'signature_date',
        'signature_deed_date',
        'value',
        'initial_fee',
        'quota_amount',
        'interest_rate',
        'cut_off_date',
        'payment_frequency',
        'payment_method',
        'status',
        'switch_payment',
        'observations',
        'category_id',
        'projection',
        'number_of_fees'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'signature_date' => 'date',
        'signature_deed_date' => 'date',
        'cut_off_date' => 'date',
        'payment_frequency' => PaymentFrequency::class,
        'payment_method' => PromisePaymentMethod::class,
        'status' => PromiseStatus::class,
        'switch_payment' => 'boolean',
        'projection' => 'array'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function deed(): HasOne
    {
        return $this->hasOne(Deed::class);
    }

    /**
     * Get the current cut off date by payment frequency.
     */
    public function getCurrentCutOffDateAttribute(): string
    {
        $number = 1;
        $check = $this->payments->where('is_initial_fee', false)->count() + 1;
        $date = now();

        if ($this->projection) {
            foreach ($this->projection as $quota) {
                if ($check == $number) {
                    $date = Carbon::parse($quota['due_date']);
                    break;
                }
                $number++;
            }
        }

        return $date;
    }
    
    /**
     * Get the current cut off date by payment frequency.
     */
    public function getCurrentQuotaAttribute(): array
    {
        $number = 1;
        $check = $this->payments->where('is_initial_fee', false)->count() + 1;
        $quota = [];

        if ($this->projection) {
            foreach ($this->projection as $quota) {
                if ($check == $number) {
                    $quota = $quota;
                    break;
                }
                $number++;
            }
        }

        return $quota;
    }

    /**
     * Get the buyer that owns the promise.
     */
    public function buyers(): BelongsToMany
    {
        return $this->belongsToMany(Buyer::class, 'promise_buyer');
    }

    /**
     * Get the parcel that owns the promise.
     */
    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    }

    public function getNumberOfPaidFeesAttribute(): int
    {
        return $this->payments->count();
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments->sum('paid_amount');
    }

    public function getHasInitialFeeAttribute(): string
    {
        return $this->payments->where('is_initial_fee', true)->count() > 0 ? 'true' : 'false';
    }

    public function getBalanceAttribute(): float
    {
        return $this->value - $this->total_paid;
    }

    public function getInterestAmountAttribute(): float
    {
        return $this->value - $this->initial_fee;
    }

    public function getInterestAmountFormattedAttribute(): string
    {
        return number_format($this->interest_amount, 0, ',', '.');
    }

    public function getTotalPaidFormattedAttribute(): string
    {
        return number_format($this->total_paid, 0, ',', '.');
    }

    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->balance, 0, ',', '.');
    }

    public function getValueFormattedAttribute(): string
    {
        return number_format($this->value, 0, ',', '.');
    }

    public function getInitialFeeFormattedAttribute(): string
    {
        return number_format($this->initial_fee, 0, ',', '.');
    }

    public function getQuotaAmountFormattedAttribute(): string
    {
        return number_format($this->quota_amount, 0, ',', '.');
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
            $query->where('number', 'LIKE', "%$search%")
                ->orWhereHas('buyers', function (Builder $query) use ($search) {
                    $query->where('names', 'LIKE', "%$search%")
                        ->orWhere('surnames', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('document_number', 'LIKE', "%$search%");
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
                $query->join('promise_buyer', 'promise_buyer.promise_id', '=', 'promises.id')
                    ->join('buyers', 'buyers.id', '=', 'promise_buyer.buyer_id')
                    ->orderBy('buyers.names', $asc ? 'asc' : 'desc');
            } else if ($column === 'parcel') {
                $query->join('parcels', 'parcels.promise_id', '=', 'promises.id')
                    ->orderBy('parcels.number', $asc ? 'asc' : 'desc');
            } else if ($column === 'promise') {
                $query->orderBy('promises.number', $asc ? 'asc' : 'desc');
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
