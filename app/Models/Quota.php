<?php

namespace App\Models;

use Database\Factories\QuotaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One quota of a promise's amortization schedule. Amounts are whole pesos.
 */
class Quota extends Model
{
    /** @use HasFactory<QuotaFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'promise_id',
        'number',
        'due_date',
        'amount',
        'interest',
        'principal',
        'balance',
        'is_extraordinary',
        'is_prepayment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'amount' => 'integer',
        'interest' => 'integer',
        'principal' => 'integer',
        'balance' => 'integer',
        'is_extraordinary' => 'boolean',
        'is_prepayment' => 'boolean',
    ];

    /**
     * Get the promise that owns the quota.
     */
    public function promise(): BelongsTo
    {
        return $this->belongsTo(Promise::class);
    }
}
