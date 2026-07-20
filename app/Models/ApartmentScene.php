<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentScene extends Model
{
    protected $fillable = [
        'apartment_id', 'is_enabled', 'default_style_slug', 'ceiling_height', 'config',
    ];

    protected $casts = [
        'is_enabled'     => 'boolean',
        'ceiling_height' => 'decimal:2',
        'config'         => 'array',
    ];

    public function apartment() { return $this->belongsTo(Apartment::class); }
    public function rooms()     { return $this->hasMany(SceneRoom::class)->orderBy('sort'); }
    public function assets()    { return $this->hasMany(SceneAsset::class); }
    public function routes()    { return $this->hasMany(CameraRoute::class); }
    public function hotspots()  { return $this->hasMany(SceneHotspot::class); }
}
