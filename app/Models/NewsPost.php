<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsPost extends Model
{
    protected $fillable = [
        'author_id', 'title', 'slug', 'excerpt', 'body',
        'image', 'category', 'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now())
                     ->orderByDesc('published_at');
    }

    /**
     * Auto-generate slug from title.
     */
    public static function makeSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    public static array $categories = [
        'news'   => 'Новости',
        'events' => 'События',
        'press'  => 'Пресса',
    ];
}
