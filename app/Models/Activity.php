<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'type','subject','body','outcome','related_type','related_id',
        'user_id','scheduled_at','completed_at','duration_seconds',
        'direction','is_done','attachments','meta',
    ];
    protected $casts = [
        'scheduled_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'is_done'       => 'boolean',
        'attachments'   => 'array',
        'meta'          => 'array',
    ];

    public function related() { return $this->morphTo(); }
    public function user()    { return $this->belongsTo(User::class); }

    public function getTypeIconAttribute(): string {
        return match($this->type) {
            'call'          => '📞',
            'email'         => '📧',
            'note'          => '📝',
            'task'          => '✅',
            'meeting'       => '🤝',
            'whatsapp'      => '💬',
            'telegram'      => '✈️',
            'status_change' => '🔄',
            default         => '📌',
        };
    }
}
