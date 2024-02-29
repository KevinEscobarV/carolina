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
        'agreement_date',
        'amount',
        'payment_date',
        'paid_amount',
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
                ->orWhere('amount', 'like', '%' . $search . '%')
                ->orWhere('payment_date', 'like', '%' . $search . '%')
                ->orWhere('paid_amount', 'like', '%' . $search . '%')
                ->orWhereHas('promise', function ($query) use ($search) {
                    $query->where('promise', 'like', '%' . $search . '%');
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
                    ->orderBy('promises.promise', $asc ? 'asc' : 'desc');
            } else if ($column === 'parcel') {
                $query->join('promises', 'promises.id', '=', 'payments.promise_id')
                    ->join('parcels', 'parcels.id', '=', 'promises.parcel_id')
                    ->orderBy('parcels.number', $asc ? 'asc' : 'desc');
            } else if ($column === 'payment_method') {
                $query->orderByRaw("FIELD(payment_method, " . implode(', ', PaymentMethod::cases()) . ") " . ($asc ? 'asc' : 'desc'));
            } else {
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            }
        }
    }
}
