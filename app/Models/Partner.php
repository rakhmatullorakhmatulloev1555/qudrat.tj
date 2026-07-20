<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name','country','type','contact_person','phone','email','website','logo',
        'contract_status','partnership_since','annual_volume','currency','notes','is_active',
    ];
    protected $casts = [
        'partnership_since' => 'date',
        'annual_volume' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
