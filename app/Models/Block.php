<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Корпус жилого комплекса (уровень 2 интерактивного выбора квартиры).
 */
class Block extends Model
{
    protected $fillable = [
        'project_id', 'name', 'slug', 'floors_total', 'facade_image',
        'masterplan_polygon', 'description', 'sort_order', 'is_published',
    ];

    protected $casts = [
        'masterplan_polygon' => 'array',
        'is_published'       => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function floors()
    {
        return $this->hasMany(Floor::class)->orderBy('number');
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function makeSlug(int $projectId, string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'block';
        $original = $slug;
        $i = 1;
        while (static::where('project_id', $projectId)->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    /** Сводка продаж по корпусу для тултипа на генплане. */
    public function salesSummary(): array
    {
        $total = $this->apartments()->count();
        $sold  = $this->apartments()->whereIn('status', ['sold', 'reserved'])->count();

        return [
            'apartments'   => $total,
            'sold_percent' => $total > 0 ? (int) round($sold * 100 / $total) : 0,
        ];
    }
}
