<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CameraRoute extends Model
{
    protected $fillable = ['apartment_scene_id', 'name', 'is_default', 'waypoints'];

    protected $casts = [
        'is_default' => 'boolean',
        'waypoints'  => 'array',
    ];

    public function scene() { return $this->belongsTo(ApartmentScene::class, 'apartment_scene_id'); }
}
