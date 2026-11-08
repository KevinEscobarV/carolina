<?php

namespace App\Models;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buyer extends Model
{
    use HasCategory;
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
        'category_id',
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
     * Get the phone_one masked attribute.
     */
    public function getPhoneOneMaskedAttribute(): string
    {
        return sprintf(
            '+%s %s %s %s',
            substr($this->phone_one, 0, 2),
            substr($this->phone_one, 2, 3),
            substr($this->phone_one, 5, 3),
            substr($this->phone_one, 8)
        );
    }

    /**
     * Get the phone_two masked attribute.
     */
    public function getPhoneTwoMaskedAttribute(): string
    {
        if (! $this->phone_two) {
            return '';
        }

        return sprintf(
            '+%s %s %s %s',
            substr($this->phone_two, 0, 2),
            substr($this->phone_two, 2, 3),
            substr($this->phone_two, 5, 3),
            substr($this->phone_two, 8)
        );
    }

    /**
     * Get the document_number masked attribute.
     */
    public function getDocumentNumberMaskedAttribute()
    {
        if (! $this->document_number) {
            return '';
        }

        if (is_numeric($this->document_number)) {
            return number_format($this->document_number, 0, '.', '.');
        } else {
            return $this->document_number;
        }
    }

    /**
     * Main phone with only its first three and last two digits, for documents anyone with the buyer's
     * document number can download. The country code 57 is left out.
     */
    public function hiddenPhone(): ?string
    {
        $digits = (string) preg_replace('/\D/', '', (string) $this->phone_one);
        if (strlen($digits) === 12 && str_starts_with($digits, '57')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) < 7) {
            return null;
        }

        return substr($digits, 0, 3).str_repeat('•', strlen($digits) - 5).substr($digits, -2);
    }

    /**
     * Email with only the first two letters of the name and the domain, for public documents.
     */
    public function hiddenEmail(): ?string
    {
        $email = mb_strtolower(trim((string) $this->email));
        if (! str_contains($email, '@')) {
            return null;
        }

        [$name, $domain] = explode('@', $email, 2);

        return mb_substr($name, 0, min(2, max(1, mb_strlen($name) - 1))).'••••@'.$domain;
    }

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
     */
    protected function getProfilePhotoUrlAttribute(): string
    {
        $names = trim(collect(explode(' ', $this->names))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($names).'&color=FFFFFF&background=F59E0B';
    }

    /**
     * Scope a query to only include buyers that match the search.
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $operator = config('database.operator');
        if ($search) {
            $query->where('names', $operator, "%$search%")
                ->orWhere('surnames', $operator, "%$search%")
                ->orWhere('email', $operator, "%$search%")
                ->orWhere(fn (Builder $query) => $query->documentContains($search));
        }
    }

    /**
     * Scope a query to only include buyers with exactly the given document,
     * ignoring dots, spaces, hyphens and letter case on both sides.
     */
    public function scopeByDocument(Builder $query, ?string $document): void
    {
        $document = static::normalizeDocument($document);

        if ($document === '') {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereRaw('UPPER('.static::normalizedDocumentColumn($query).') = ?', [$document]);
    }

    /**
     * Scope a query to buyers whose document contains the given text, so "1.115.918" and "1115918"
     * find the same buyer whether the document was saved with dots or without them.
     */
    public function scopeDocumentContains(Builder $query, ?string $search): void
    {
        $search = static::normalizeDocument($search);

        if ($search === '') {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereRaw(
            'UPPER('.static::normalizedDocumentColumn($query).') '.config('database.operator').' ?',
            ["%{$search}%"]
        );
    }

    /**
     * Remove dots, spaces and hyphens from a document number and uppercase it.
     */
    protected static function normalizeDocument(?string $document): string
    {
        return strtoupper(preg_replace('/[\s.\-]/', '', (string) $document));
    }

    /**
     * SQL expression for the document number without dots, spaces and hyphens.
     */
    protected static function normalizedDocumentColumn(Builder $query): string
    {
        $column = $query->qualifyColumn('document_number');

        return "REPLACE(REPLACE(REPLACE({$column}, '.', ''), ' ', ''), '-', '')";
    }

    /**
     * Scope a query to sort buyers by the specified column.
     */
    public function scopeSort(Builder $query, ?string $column, bool $asc): void
    {
        if ($column) {
            $query->orderBy($column, $asc ? 'asc' : 'desc');
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
