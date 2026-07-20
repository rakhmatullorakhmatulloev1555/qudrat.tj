<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'role', 'company', 'text', 'rating', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'rating'     => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Initials from name (max 2 chars).
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        return strtoupper(
            implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2)))
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
