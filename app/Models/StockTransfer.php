<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'batch_id',
        'from_showroom_id',
        'to_showroom_id',
        'tyre_id',
        'quantity',
        'date',
        'notes'
    ];

    public function getBatchKeyAttribute(): string
    {
        return $this->batch_id ?: 'legacy-' . $this->id;
    }

    public function fromShowroom()
    {
        return $this->belongsTo(Showroom::class, 'from_showroom_id');
    }

    public function toShowroom()
    {
        return $this->belongsTo(Showroom::class, 'to_showroom_id');
    }

    public function tyre()
    {
        return $this->belongsTo(Tyre::class);
    }
}
