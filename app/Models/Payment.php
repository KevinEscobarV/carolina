<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, HasCategory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bill_number',
        'agreement_date',
        'agreement_amount',
        'payment_date',
        'paid_amount',
        'bank',
        'payment_method',
        'observations',
        'bill_path',
        'promise_id',
        'category_id',
        'is_initial_fee',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'payment_date' => 'date',
        'payment_method' => PaymentMethod::class,
        'is_initial_fee' => 'boolean',
    ];

    public function getPaidAmountFormattedAttribute(): string
    {
        return number_format($this->paid_amount, 0, ',', '.');
    }

    public function getAgreementAmountFormattedAttribute(): string
    {
        return number_format($this->agreement_amount, 0, ',', '.');
    }

    /**
     * Get the promise that owns the payment.
     */
    public function promise(): BelongsTo
    {
        return $this->belongsTo(Promise::class);
    }

    /**
     * Get the parcel that owns the payment.
     */
    public function parcel(): HasOneThrough
    {
        return $this->hasOneThrough(Parcel::class, Promise::class);
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
        $operator = config('database.operator');
        if ($search)
            $query->where('agreement_date', $operator, '%' . $search . '%')
                ->orWhere('agreement_amount', $operator, '%' . $search . '%')
                ->orWhere('payment_date', $operator, '%' . $search . '%')
                ->orWhere('paid_amount', $operator, '%' . $search . '%')
                ->orWhereHas('promise', function ($query) use ($search, $operator) {
                    $query->where('number', $operator, '%' . $search . '%')
                        ->orWhereHas('buyers', function ($query) use ($search, $operator) {
                            $query->where('names', $operator, "%$search%")
                                ->orWhere('surnames', $operator, "%$search%")
                                ->orWhere('email', $operator, "%$search%")
                                ->orWhere('document_number', $operator, "%$search%");
                        });
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
            if ($column === 'promise') {
                $query->join('promises', 'promises.id', '=', 'payments.promise_id')
                    ->orderBy('promises.number', $asc ? 'asc' : 'desc');
            } else if ($column === 'parcel') {
                $query->join('promises', 'promises.id', '=', 'payments.promise_id')
                    ->join('parcels', 'parcels.id', '=', 'promises.parcel_id')
                    ->orderBy('parcels.number', $asc ? 'asc' : 'desc');
            } else {
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            }
        }
    }

    /**
     * Scope a query to only include payments that match the promise ids.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array<int>  $promiseIds
     * @return void
     */
    public function scopePromiseFilter(Builder $query, array $promiseIds): void
    {
        if ($promiseIds) $query->whereIn('promise_id', $promiseIds);
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
