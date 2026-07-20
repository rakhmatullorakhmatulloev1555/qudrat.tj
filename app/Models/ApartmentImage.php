<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentImage extends Model
{
    protected $fillable = ['apartment_id', 'path', 'alt', 'sort', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
