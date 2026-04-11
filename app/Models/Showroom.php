<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showroom extends Model
{
    protected $fillable = [
        'name',
        'location'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}