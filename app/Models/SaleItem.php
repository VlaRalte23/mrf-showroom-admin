<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{

    protected $fillable = [
        'sale_id',
        'tyre_id',
        'quantity',
        'price'
    ];

    public function tyre()
    {
        return $this->belongsTo(Tyre::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

}