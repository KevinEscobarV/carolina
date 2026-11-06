<?php

namespace App\Models;

use App\Enums\InterestRateType;
use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Enums\RebalanceMode;
use App\Models\Traits\HasCategory;
use App\Services\Amortization\Coverage;
use App\Services\Amortization\InterestRate;
use App\Services\Amortization\ScheduleTerms;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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

    /**
     * What the schedule is calculated from: the amount financed, rate, dates and extraordinary quotas.
     */
    public function scheduleTerms(): ScheduleTerms
    {
        $signature = $this->signature_date ? CarbonImmutable::parse($this->signature_date) : null;

        return new ScheduleTerms(
            principal: (int) round((float) $this->value - (float) $this->initial_fee),
            firstDueDate: $this->cut_off_date ? CarbonImmutable::parse($this->cut_off_date) : ($signature ?? CarbonImmutable::today()),
            frequency: $this->payment_frequency ?? PaymentFrequency::MONTHLY,
            rate: new InterestRate((float) $this->interest_rate, $this->interest_rate_type ?? InterestRateType::MONTHLY),
            startDate: $signature,
            extraordinaryMonths: array_map('intval', $this->extraordinary_months ?? []),
            extraordinaryAmount: (int) $this->extraordinary_amount,
        );
    }

    /**
     * How the following quotas adapt when one changes, as the promise chose for extra payments.
     */
    public function rebalanceMode(): RebalanceMode
    {
        return RebalanceMode::fromSwitchPayment((bool) $this->switch_payment);
    }

    /**
     * The quotas as editable rows of the schedule calculator.
     *
     * @return list<array{number: int, due_date: string, amount: int, interest: int, principal: int, balance: int, is_extraordinary: bool}>
     */
    public function scheduleRows(): array
    {
        return $this->quotas->map(fn (Quota $quota) => [
            'number' => $quota->number,
            'due_date' => $quota->due_date->toDateString(),
            'amount' => $quota->amount,
            'interest' => $quota->interest,
            'principal' => $quota->principal,
            'balance' => $quota->balance,
            'is_extraordinary' => $quota->is_extraordinary,
        ])->all();
    }

    /**
     * Replaces the schedule. The JSON projection is kept in step for the screens that still read it.
     *
     * @param  list<array<string, mixed>>  $rows
     */
    public function saveSchedule(array $rows): void
    {
        $rows = array_values($rows);

        DB::transaction(function () use ($rows) {
            Quota::where('promise_id', $this->id)->delete();

            $this->quotas()->createMany(array_map(fn (array $row) => Arr::only($row, [
                'number', 'due_date', 'amount', 'interest', 'principal', 'balance', 'is_extraordinary',
            ]), $rows));

            $this->forceFill([
                'number_of_fees' => count($rows),
                'projection' => array_map(fn (array $row) => [
                    'number' => $row['number'],
                    'due_date' => $row['due_date'],
                    'payment_amount' => $row['amount'],
                    'interest_payment' => $row['interest'],
                    'principal_payment' => $row['principal'],
                    'remaining_balance' => $row['balance'],
                ], $rows),
            ])->save();
        });

        $this->unsetRelation('quotas');
    }

    /**
     * How the payments (without the initial fee) cover the quotas.
     */
    public function coverage(): Coverage
    {
        return Coverage::of($this->scheduleRows(), (float) $this->payments->where('is_initial_fee', false)->sum('paid_amount'));
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
