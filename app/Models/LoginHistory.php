<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_history';
    public $timestamps = false;
    protected $fillable = [
        'user_id','email','ip_address','country','city',
        'device','browser','platform','status','two_fa_used','user_agent','created_at',
    ];
    protected $casts = [
        'two_fa_used' => 'boolean',
        'created_at'  => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public static function record(array $data): void
    {
        $request = request();
        $ua      = $request->userAgent() ?? '';

        // Simple UA parsing (no extra package needed)
        $device  = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'mobile'
                 : (preg_match('/Tablet|iPad/i', $ua)          ? 'tablet' : 'desktop');
        $browser = preg_match('/Edge\/|Edg\//i', $ua)  ? 'Edge'
                 : (preg_match('/Firefox/i', $ua)       ? 'Firefox'
                 : (preg_match('/Chrome/i', $ua)        ? 'Chrome'
                 : (preg_match('/Safari/i', $ua)        ? 'Safari' : 'Other')));

        static::create(array_merge([
            'ip_address' => $request->ip(),
            'user_agent' => substr($ua, 0, 500),
            'device'     => $device,
            'browser'    => $browser,
            'status'     => 'success',
            'two_fa_used'=> false,
            'created_at' => now(),
        ], $data));
    }
}
