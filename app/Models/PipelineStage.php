<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    protected $fillable = [
        'pipeline_id','name','key','color','probability',
        'position','is_won','is_lost','auto_actions',
    ];
    protected $casts = [
        'is_won'       => 'boolean',
        'is_lost'      => 'boolean',
        'auto_actions' => 'array',
        'probability'  => 'integer',
        'position'     => 'integer',
    ];

    public function pipeline() { return $this->belongsTo(Pipeline::class); }
    public function deals()    { return $this->hasMany(Deal::class, 'stage_id'); }
    public function leads()    { return $this->hasMany(Lead::class, 'pipeline_stage', 'key'); }
}
