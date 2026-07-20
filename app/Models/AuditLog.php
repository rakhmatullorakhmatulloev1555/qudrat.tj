<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id','user_name','user_email','action','module',
        'model_type','model_id','model_label',
        'old_values','new_values','description',
        'ip_address','user_agent','url','method','extra','created_at',
    ];
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'extra'      => 'array',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    /** Log an action — call from anywhere */
    public static function record(
        string $action,
        string $module,
        ?string $description = null,
        array $extra = [],
        mixed $model = null,
        array $oldValues = [],
        array $newValues = [],
    ): void {
        $user    = auth()->user();
        $request = request();
        static::create([
            'user_id'    => $user?->id,
            'user_name'  => $user?->name,
            'user_email' => $user?->email,
            'action'     => $action,
            'module'     => $module,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'model_label'=> $model?->name ?? $model?->title ?? null,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'description'=> $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url'        => $request?->fullUrl(),
            'method'     => $request?->method(),
            'extra'      => $extra ?: null,
            'created_at' => now(),
        ]);
    }
}
