<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','phone','email','message','status','source',
        'interest','assigned_to','client_id','notes','contacted_at',
        'score','temperature','utm_source','utm_medium','utm_campaign',
        'referrer_url','landing_page','budget_range','pipeline_stage',
        'last_activity_at','next_follow_up_at','tags',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at'      => 'datetime',
            'last_activity_at'  => 'datetime',
            'next_follow_up_at' => 'datetime',
            'tags'              => 'array',
            'score'             => 'integer',
        ];
    }

    public function assignee()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function client()    { return $this->belongsTo(Client::class); }
    public function activities(){ return $this->morphMany(Activity::class, 'related')->latest(); }
    public function deals()     { return $this->hasMany(Deal::class); }

    public static function statusLabel(string $status): string
    {
        return match($status) {
            'new'         => 'Новая',
            'in_progress' => 'В работе',
            'success'     => 'Успешно',
            'rejected'    => 'Отказ',
            default       => $status,
        };
    }

    public static function stageLabel(string $stage): string
    {
        return match($stage) {
            'new'       => 'Новый',
            'contacted' => 'Контакт',
            'qualified' => 'Квалифицирован',
            'proposal'  => 'Предложение',
            'converted' => 'Продажа',
            'lost'      => 'Отказ',
            default     => $stage,
        };
    }

    public function recalculateScore(): int
    {
        $score = 0;
        if ($this->phone)    $score += 15;
        if ($this->email)    $score += 10;
        if ($this->budget_range) $score += 20;
        $score += match($this->temperature ?? 'cold') {
            'hot'  => 30,
            'warm' => 15,
            default => 0,
        };
        $actCount = $this->activities()->count();
        $score += min($actCount * 5, 25);
        $this->update(['score' => min($score, 100)]);
        return $score;
    }
}
