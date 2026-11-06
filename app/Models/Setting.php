<?php

namespace App\Models;

use App\Models\Scopes\CategoryScope;
use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasCategory, HasFactory;

    /**
     * Usury rate when none has been saved yet: November 2026, consumer and ordinary credit (Superfinanciera).
     */
    public const DEFAULT_USURY_RATE = '28.34';

    protected $fillable = [
        'key',
        'value',
        'description',
        'category_id',
    ];

    /**
     * Days after a due date before the quota counts as overdue, per campaign (0 when not set).
     */
    public static function graceDays(?int $categoryId): int
    {
        $byCategory = once(fn () => static::withoutGlobalScope(CategoryScope::class)
            ->where('key', 'grace_days')
            ->pluck('value', 'category_id'));

        return (int) ($byCategory[$categoryId] ?? 0);
    }

    /**
     * The usury rate in force (effective annual %), the same for every campaign.
     */
    public static function usuryRate(): self
    {
        return static::withoutGlobalScope(CategoryScope::class)->firstOrCreate(
            ['key' => 'usury_rate'],
            ['value' => self::DEFAULT_USURY_RATE, 'description' => 'Tasa de usura vigente (% efectivo anual)'],
        );
    }
}
