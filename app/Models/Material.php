<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['slug', 'name', 'type', 'params', 'preview_path', 'is_active'];

    protected $casts = [
        'params'    => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q) { return $q->where('is_active', true); }
}
