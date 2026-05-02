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

    protected $casts = [
        'date' => 'date',
    ];

    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function debt()
    {
        return $this->hasOne(Debt::class);
    }

    public function hasDebt()
    {
        return $this->debt()->exists();
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

}