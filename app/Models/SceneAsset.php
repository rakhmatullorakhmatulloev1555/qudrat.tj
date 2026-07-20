<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SceneAsset extends Model
{
    protected $fillable = [
        'apartment_scene_id', 'interior_style_id', 'scene_room_id',
        'kind', 'name', 'path', 'disk', 'format', 'size_bytes', 'status', 'meta',
    ];

    protected $casts = [
        'meta'       => 'array',
        'size_bytes' => 'integer',
    ];

    /** Публичный URL ассета. */
    public function getUrlAttribute(): ?string
    {
        if (!$this->path) return null;
        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }

    public function scene() { return $this->belongsTo(ApartmentScene::class, 'apartment_scene_id'); }

    public function scopeKind($q, string $kind) { return $q->where('kind', $kind); }
}
