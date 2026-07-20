<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    protected $fillable = ['name','type','is_default','is_active'];
    protected $casts    = ['is_default'=>'boolean','is_active'=>'boolean'];

    public function stages() {
        return $this->hasMany(PipelineStage::class)->orderBy('position');
    }
    public function deals() {
        return $this->hasMany(Deal::class);
    }
}
