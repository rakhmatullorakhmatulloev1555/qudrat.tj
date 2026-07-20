<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SceneHotspot extends Model
{
    protected $fillable = [
        'apartment_scene_id', 'scene_room_id', 'name', 'description',
        'position', 'icon', 'target_room_slug', 'meta',
    ];

    protected $casts = [
        'position' => 'array',
        'meta'     => 'array',
    ];

    public function scene() { return $this->belongsTo(ApartmentScene::class, 'apartment_scene_id'); }
    public function room()  { return $this->belongsTo(SceneRoom::class, 'scene_room_id'); }
}
