<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'birth_date', 'passport',
        'type', 'status', 'source', 'assigned_to', 'notes',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
