<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'title', 'alt', 'image_path', 'category', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public static array $categories = [
        'building' => 'Здание',
        'interior' => 'Интерьер',
        'progress' => 'Ход строительства',
        'events'   => 'Мероприятия',
    ];
}
