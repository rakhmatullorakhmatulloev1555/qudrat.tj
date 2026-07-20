<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'status', 'class', 'city', 'address',
        'description', 'price_from', 'price_to', 'currency',
        'floors_count', 'apartments_count', 'completion_year',
        'cover_image', 'is_published', 'masterplan_image', 'masterplan_pois',
    ];

    protected function casts(): array
    {
        return [
            'is_published'    => 'boolean',
            'price_from'      => 'decimal:2',
            'price_to'        => 'decimal:2',
            'masterplan_pois' => 'array',
        ];
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function availableCount(): int
    {
        return $this->apartments()->where('status', 'available')->count();
    }
}
