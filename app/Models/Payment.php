<?php

namespace App\Models;

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

    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CARD = 'card';
    const PAYMENT_METHOD_CHECK = 'check';
    const PAYMENT_METHOD_TRANSFER = 'transfer';
    const PAYMENT_METHOD_DEPOSIT = 'deposit';

    const PAYMENT_METHODS = [
        [
            'value' => self::PAYMENT_METHOD_CASH,
            'label' => 'Efectivo',
        ],
        [
            'value' => self::PAYMENT_METHOD_CARD,
            'label' => 'Tarjeta de crédito o débito',
        ],
        [
            'value' => self::PAYMENT_METHOD_CHECK,
            'label' => 'Cheque',
        ],
        [
            'value' => self::PAYMENT_METHOD_TRANSFER,
            'label' => 'Transferencia bancaria',
        ],
        [
            'value' => self::PAYMENT_METHOD_DEPOSIT,
            'label' => 'Depósito',
        ],
    ];

    /**
     * Get the payment method label.
     * 
     * @return string
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return collect(self::PAYMENT_METHODS)->firstWhere('value', $this->payment_method)['label'] ?? 'N/A';
    }


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
