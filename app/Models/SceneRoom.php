<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SceneRoom extends Model
{
    protected $fillable = [
        'apartment_scene_id', 'slug', 'name', 'icon', 'sort',
        'camera_pos', 'camera_target', 'model_asset_id', 'config',
    ];

    protected $casts = [
        'camera_pos'    => 'array',
        'camera_target' => 'array',
        'config'        => 'array',
    ];

    public function scene() { return $this->belongsTo(ApartmentScene::class, 'apartment_scene_id'); }
    public function model() { return $this->belongsTo(SceneAsset::class, 'model_asset_id'); }
}
