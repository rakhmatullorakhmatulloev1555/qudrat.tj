<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteriorStyle extends Model
{
    protected $fillable = [
        'slug', 'name', 'accent_hex', 'background_hex',
        'materials', 'lighting', 'hdri_path', 'is_active', 'sort',
    ];

    protected $casts = [
        'materials' => 'array',
        'lighting'  => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('sort'); }
}
