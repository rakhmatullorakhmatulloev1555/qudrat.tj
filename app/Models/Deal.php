<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'uuid','title','pipeline_id','stage_id','lead_id','contact_id','apartment_id',
        'amount','currency','probability','expected_close_date','closed_at',
        'status','assigned_to','created_by','notes','tags',
    ];
    protected $casts = [
        'expected_close_date' => 'date',
        'closed_at'           => 'datetime',
        'amount'              => 'decimal:2',
        'tags'                => 'array',
    ];

    protected static function booted(): void {
        static::creating(function ($deal) {
            $deal->uuid ??= \Illuminate\Support\Str::uuid();
        });
    }

    public function pipeline()  { return $this->belongsTo(Pipeline::class); }
    public function stage()     { return $this->belongsTo(PipelineStage::class, 'stage_id'); }
    public function lead()      { return $this->belongsTo(Lead::class); }
    public function assignee()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function activities(){ return $this->morphMany(Activity::class, 'related'); }
}
