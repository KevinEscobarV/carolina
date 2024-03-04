<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

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
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'payment_date' => 'date',
        'payment_method' => PaymentMethod::class,
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
        if ($search)
            $query->where('agreement_date', 'like', '%' . $search . '%')
                ->orWhere('agreement_amount', 'like', '%' . $search . '%')
                ->orWhere('payment_date', 'like', '%' . $search . '%')
                ->orWhere('paid_amount', 'like', '%' . $search . '%')
                ->orWhereHas('promise', function ($query) use ($search) {
                    $query->where('number', 'like', '%' . $search . '%')
                        ->orWhereHas('buyers', function ($query) use ($search) {
                            $query->where('names', 'like', "%$search%")
                            ->orWhere('surnames', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%")
                            ->orWhere('document_number', 'like', "%$search%");
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
}
