<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'buyer_id',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'payment_date' => 'date',
        'payment_method' => PaymentMethod::class,
    ];


    /**
     * Get the promise that owns the payment.
     */
    public function promise()
    {
        return $this->belongsTo(Promise::class);
    }

    /**
     * Get the buyer that owns the payment.
     */
    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Get the parcel that owns the payment.
     */
    public function parcel(): HasOneThrough
    {
        return $this->hasOneThrough(Parcel::class, Promise::class);
    }
}
