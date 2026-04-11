<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'tyre_id',
        'quantity',
        'price'
    ];
    
    public function tyre() {
        return $this->belongsTo(Tyre::class);
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
}
