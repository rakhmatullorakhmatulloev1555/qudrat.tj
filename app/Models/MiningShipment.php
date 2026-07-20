<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MiningShipment extends Model
{
    protected $fillable = [
        'date','site','coal_type','volume','price_per_ton','currency',
        'buyer','destination','status','quality_kcal','notes',
    ];
    protected $casts = ['date' => 'date', 'volume' => 'decimal:2', 'price_per_ton' => 'decimal:2'];

    public function totalValue(): float
    {
        return (float)($this->volume * $this->price_per_ton);
    }
}
