<?php

namespace App\Models;

use App\Enums\InterestRateType;
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
    use HasCategory, HasFactory, SoftDeletes;

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
        'interest_rate_type',
        'cut_off_date',
        'payment_frequency',
        'payment_method',
        'status',
        'switch_payment',
        'observations',
        'category_id',
        'projection',
        'number_of_fees',
        'extraordinary_months',
        'extraordinary_amount',
        'switch_quota',
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
        'switch_quota' => 'boolean',
        'projection' => 'array',
        'interest_rate_type' => InterestRateType::class,
        'extraordinary_months' => 'array',
        'extraordinary_amount' => 'integer',
    ];

    /**
     * Get the quotas of the amortization schedule, in order.
     */
    public function quotas(): HasMany
    {
        return $this->hasMany(Quota::class)->orderBy('number');
    }

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

    public function getCurrentQuotaAmountFormattedAttribute()
    {
        return $this->current_quota ? number_format($this->current_quota['payment_amount'], 0, ',', '.') : null;
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
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $operator = config('database.operator');
        if ($search) {
            $query->where('number', $operator, "%$search%")
                ->orWhereHas('buyers', function (Builder $query) use ($search, $operator) {
                    $query->where('names', $operator, "%$search%")
                        ->orWhere('surnames', $operator, "%$search%")
                        ->orWhere('email', $operator, "%$search%")
                        ->orWhere(fn (Builder $query) => $query->documentContains($search));
                });
        }
    }

    /**
     * Scope a query to sort buyers by the specified column.
     */
    public function scopeSort(Builder $query, ?string $column, bool $asc): void
    {
        if ($column) {
            if ($column === 'buyer') {
                // A subquery keeps one row per promise and the promise's own columns
                $query->orderBy(
                    Buyer::select('names')
                        ->join('promise_buyer', 'promise_buyer.buyer_id', '=', 'buyers.id')
                        ->whereColumn('promise_buyer.promise_id', 'promises.id')
                        ->limit(1),
                    $asc ? 'asc' : 'desc'
                );
            } elseif ($column === 'parcel') {
                $query->join('parcels', 'parcels.promise_id', '=', 'promises.id')
                    ->orderBy('parcels.number', $asc ? 'asc' : 'desc');
            } elseif ($column === 'promise') {
                $query->orderBy('promises.number', $asc ? 'asc' : 'desc');
            } else {
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            }
        }
    }

    /**
     * Scope a query to only include buyers that are trashed.
     */
    public function scopeTrash(Builder $query, bool $onlyTrash): void
    {
        if ($onlyTrash) {
            $query->onlyTrashed();
        }
    }
}
