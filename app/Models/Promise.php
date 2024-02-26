<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'buyer_id',
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

    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CREDIT = 'credit';

    const PAYMENT_METHODS = [
        [
            'value' => self::PAYMENT_METHOD_CASH,
            'label' => 'Contado',
        ],
        [
            'value' => self::PAYMENT_METHOD_CREDIT,
            'label' => 'Crédito',
        ],
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELED = 'canceled';

    const STATUSES = [
        [
            'value' => self::STATUS_PENDING,
            'label' => 'Pendiente',
        ],
        [
            'value' => self::STATUS_PAID,
            'label' => 'Pagado',
        ],
        [
            'value' => self::STATUS_CANCELED,
            'label' => 'Cancelado',
        ],
    ];

    /**
     * Get the status label.
     * 
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return collect(self::STATUSES)->firstWhere('value', $this->status)['label'] ?? 'N/A';
    }

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
     * Get the buyer that owns the promise.
     */
    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Get the parcel that owns the promise.
     */
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}
