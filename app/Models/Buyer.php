<?php

namespace App\Models;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buyer extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'document_type' => DocumentType::class,
        'civil_status' => CivilStatus::class,
    ];


    /**
     * Get the promises for the buyer.
     */
    public function promises(): BelongsToMany
    {
        return $this->belongsToMany(Promise::class, 'promise_buyer');
    }

    /**
     * Get the last payment for the buyer.
     */
    public function getLastPaymentAttribute()
    {
        return $this->promises->map(function ($promise) {
            return $promise->payments->last();
        })->last();  
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function getProfilePhotoUrlAttribute(): string
    {
        $names = trim(collect(explode(' ', $this->names))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($names) . '&color=FFFFFF&background=F59E0B';
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
            $query->where('names', 'like', "%$search%")
                ->orWhere('surnames', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('document_number', 'like', "%$search%");
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
        if ($column) $query->orderBy($column, $asc ? 'asc' : 'desc');
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
