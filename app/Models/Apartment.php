<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'block_id', 'number', 'floor', 'rooms', 'area', 'price',
        'currency', 'status', 'finish', 'plan_image', 'polygon', 'client_id', 'notes',
        'ceiling_height', 'bathrooms', 'balcony', 'view_type',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'decimal:2',
            'area'           => 'decimal:2',
            'ceiling_height' => 'decimal:2',
            'polygon'        => 'array',
            'balcony'        => 'boolean',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scene()
    {
        return $this->hasOne(ApartmentScene::class);
    }

    public function images()
    {
        return $this->hasMany(ApartmentImage::class)->orderBy('sort')->orderBy('id');
    }
}
