<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    protected $fillable = [
        'showroom_id',
        'date',
        'notes'
    ];

    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

}