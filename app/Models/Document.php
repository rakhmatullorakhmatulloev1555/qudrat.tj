<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title','type','related_type','related_id','related_name',
        'status','file_path','original_filename','file_size',
        'issued_at','expires_at','signed_by','notes',
    ];
    protected $casts = ['issued_at' => 'date', 'expires_at' => 'date'];

    public function isExpiringSoon(): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= 30 && $this->expires_at->isFuture();
    }
}
