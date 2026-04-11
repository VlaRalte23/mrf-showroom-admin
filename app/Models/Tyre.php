<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tyre extends Model
{
    protected $fillable = [
        'tyre_size',
        'pattern',
        'category',
        'price'
    ];

    public function stockMovements(){
        return $this->hasMany(StockMovement::class);
    }

public function getStockByShowroom($showroomId)
{
    $invoiceStock = DB::table('invoice_items')
        ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->where('invoice_items.tyre_id', $this->id)
        ->where('invoices.showroom_id', $showroomId)
        ->sum('invoice_items.quantity');

    $saleStock = DB::table('sale_items')
        ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
        ->where('sale_items.tyre_id', $this->id)
        ->where('sales.showroom_id', $showroomId)
        ->sum('sale_items.quantity');

    $incomingTransfers = DB::table('stock_transfers')
        ->where('tyre_id', $this->id)
        ->where('to_showroom_id', $showroomId)
        ->sum('quantity');

    $outgoingTransfers = DB::table('stock_transfers')
        ->where('tyre_id', $this->id)
        ->where('from_showroom_id', $showroomId)
        ->sum('quantity');

    return $invoiceStock - $saleStock + $incomingTransfers - $outgoingTransfers;
}
}
