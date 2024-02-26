<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buyer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'names',
        'surnames',
        'email',
        'document_type',
        'document_number',
        'civil_status',
        'phone_one',
        'phone_two',
        'address',
    ];

    const CIVIL_STATUS_SINGLE = 'single';
    const CIVIL_STATUS_MARRIED = 'married';
    const CIVIL_STATUS_DIVORCED = 'divorced';
    const CIVIL_STATUS_WIDOWER = 'widower';

    const CIVIL_STATUSES = [
        self::CIVIL_STATUS_SINGLE => 'Soltero',
        self::CIVIL_STATUS_MARRIED => 'Casado',
        self::CIVIL_STATUS_DIVORCED => 'Divorciado',
        self::CIVIL_STATUS_WIDOWER => 'Viudo',
    ];

    const DOCUMENT_TYPE_CC = 'cc';
    const DOCUMENT_TYPE_CE = 'ce';
    const DOCUMENT_TYPE_TI = 'ti';
    const DOCUMENT_TYPE_NIT = 'nit';
    const DOCUMENT_TYPE_RUT = 'rut';
    const DOCUMENT_TYPE_PASSPORT = 'passport';

    const DOCUMENT_TYPES = [
        self::DOCUMENT_TYPE_CC => 'Cédula de ciudadanía',
        self::DOCUMENT_TYPE_CE => 'Cédula de extranjería',
        self::DOCUMENT_TYPE_TI => 'Tarjeta de identidad',
        self::DOCUMENT_TYPE_NIT => 'NIT',
        self::DOCUMENT_TYPE_RUT => 'RUT',
        self::DOCUMENT_TYPE_PASSPORT => 'Pasaporte',
    ];

    /**
     * Get the civil status label.
     */
    public function getCivilStatusLabelAttribute(): string
    {
        return self::CIVIL_STATUSES[$this->civil_status];
    }

    /**
     * Get the document type label.
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type];
    }


    /**
     * Get the promises for the buyer.
     */
    public function promises(): HasMany
    {
        return $this->hasMany(Promise::class);
    }

    /**
     * Get the parcels for the buyer.
     */
    public function parcels(): HasManyThrough
    {
        return $this->hasManyThrough(Parcel::class, Promise::class);
    }

    /**
     * Get the payments for the buyer.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
