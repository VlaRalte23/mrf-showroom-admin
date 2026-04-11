<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;


class Invoice extends Model
{
    protected $fillable = [
        'showroom_id',
        'invoice_no',
        'date'
    ];
    
    public function showroom() {
        return $this->belongsTo(Showroom::class);
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }
}
