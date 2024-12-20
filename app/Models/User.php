<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'expanded_sidebar',
        'current_category_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'expanded_sidebar' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the name role attribute.
     */
    public function getNameRoleAttribute(): string
    {
        return $this->roles->pluck('name')->join(', ');
    }

    /**
     * Toggle the user's sidebar preference.
     *
     * @return void
     */
    public function toggleSidebar()
    {
        $this->expanded_sidebar = !$this->expanded_sidebar;

        $this->save();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=F59E0B';
    }

    /**
     * Determine if the given team is the current team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function isCurrentCategory($category)
    {
        return $category->id === $this->currentCategory->id;
    }

    /**
     * Get the current category of the user's context.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentCategory()
    {
        if (is_null($this->current_category_id) && $this->id) {
            $this->switchCategory(Category::first());
        }

        return $this->belongsTo(Category::class, 'current_category_id')->withDefault();
    }

    /**
     * Switch the user's context to the given category.
     *
     * @param  mixed  $category
     * @return bool
     */
    public function switchCategory($category)
    {
        if (! $category instanceof Category) {
            return false;
        }

        $this->forceFill([
            'current_category_id' => $category->id,
        ])->save();

        $this->setRelation('currentCategory', $category);

        return true;
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
        $operator = config('database.operator');
        if ($search) {
            $query->where('name', $operator, '%' . $search . '%')
                ->orWhere('email', $operator, '%' . $search . '%');
        }
    }

    /**
     * Scope a query to only include buyers that match the search.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return void
     */
    public function scopeSort(Builder $query, string $column = null, bool $asc): void
    {
        if ($column) {
            $query->orderBy($column, $asc ? 'asc' : 'desc');
        }
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
