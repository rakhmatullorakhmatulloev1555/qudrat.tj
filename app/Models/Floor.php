<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Этаж корпуса (уровень 3 интерактивного выбора квартиры).
 */
class Floor extends Model
{
    protected $fillable = ['block_id', 'number', 'plan_image', 'facade_polygon'];

    protected $casts = [
        'facade_polygon' => 'array',
    ];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /** Квартиры этажа (по номеру этажа в рамках корпуса). */
    public function apartments()
    {
        return Apartment::where('block_id', $this->block_id)
            ->where('floor', $this->number);
    }
}
