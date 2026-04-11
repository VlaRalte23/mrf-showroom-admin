<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'showroom_id',
        'tyre_id',
        'quantity',
        'type'
    ];

    public function tyre(){
        return $this->belongsTo(Tyre::class);
    }

    public function showroom() {
        return $this->belongsTo(Showroom::class);
    }
}
