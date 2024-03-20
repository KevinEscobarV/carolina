<?php

namespace App\Models;

use App\Models\Traits\HasCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasCategory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'category_id'
    ];
}
