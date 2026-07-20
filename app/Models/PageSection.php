<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'page','section','locale','content','settings','is_active','position','updated_by',
    ];
    protected $casts = [
        'content'   => 'array',
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];

    /** Get all sections for a page+locale as key=>content map */
    public static function forPage(string $page, string $locale = 'ru'): array
    {
        return static::where('page', $page)
            ->where('locale', $locale)
            ->where('is_active', true)
            ->orderBy('position')
            ->get()
            ->keyBy('section')
            ->map(fn($s) => $s->content)
            ->toArray();
    }

    public function editor() { return $this->belongsTo(User::class, 'updated_by'); }
}
