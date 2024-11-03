<?php

namespace App\Models\Traits;

use App\Models\Category;
use App\Models\Scopes\CategoryScope;
use Illuminate\Support\Facades\Auth;

trait HasCategory
{
    /**
     * Boot the HasCategory trait for a model.
     *
     * @return void
     */
    public static function bootHasCategory(): void
    {
        static::creating(function ($model) {
            $model->category_id = Auth::user()->currentCategory->id;
        });

        static::addGlobalScope(new CategoryScope);
    }

    /**
     * Get the category that owns the model.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
