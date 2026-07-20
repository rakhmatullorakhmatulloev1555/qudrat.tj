<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConstructionUpdate extends Model
{
    protected $fillable = [
        'update_date',
        'title',
        'description',
        'progress',
        'is_current',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'update_date'  => 'date',
            'progress'     => 'integer',
            'is_current'   => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Current overall progress percentage (from the latest current record).
     */
    public static function overallProgress(): int
    {
        $current = static::where('is_current', true)->where('is_published', true)->latest('update_date')->first();
        return $current ? $current->progress : 0;
    }
}
