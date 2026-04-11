<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class StockReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected string $view = 'filament.pages.stock-report';

    public $stocks = [];
    public $showrooms = [];

    public function mount()
    {
        $this->showrooms = DB::table('showrooms')
            ->orderBy('name')
            ->get();

        $purchases = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select(
                'invoice_items.tyre_id',
                'invoices.showroom_id',
                DB::raw('SUM(invoice_items.quantity) as purchased')
            )
            ->groupBy('invoice_items.tyre_id', 'invoices.showroom_id');

        $sales = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->select(
                'sale_items.tyre_id',
                'sales.showroom_id',
                DB::raw('SUM(sale_items.quantity) as sold')
            )
            ->groupBy('sale_items.tyre_id', 'sales.showroom_id');

        $incoming_transfers = DB::table('stock_transfers')
            ->select(
                'tyre_id',
                'to_showroom_id as showroom_id',
                DB::raw('SUM(quantity) as transferred_in')
            )
            ->groupBy('tyre_id', 'to_showroom_id');

        $outgoing_transfers = DB::table('stock_transfers')
            ->select(
                'tyre_id',
                'from_showroom_id as showroom_id',
                DB::raw('SUM(quantity) as transferred_out')
            )
            ->groupBy('tyre_id', 'from_showroom_id');

        $this->stocks = DB::table('tyres')
            ->crossJoin('showrooms')

            ->leftJoinSub($purchases, 'p', function ($join) {
                $join->on('p.tyre_id', '=', 'tyres.id')
                     ->on('p.showroom_id', '=', 'showrooms.id');
            })

            ->leftJoinSub($sales, 's', function ($join) {
                $join->on('s.tyre_id', '=', 'tyres.id')
                     ->on('s.showroom_id', '=', 'showrooms.id');
            })

            ->leftJoinSub($incoming_transfers, 'it', function ($join) {
                $join->on('it.tyre_id', '=', 'tyres.id')
                     ->on('it.showroom_id', '=', 'showrooms.id');
            })

            ->leftJoinSub($outgoing_transfers, 'ot', function ($join) {
                $join->on('ot.tyre_id', '=', 'tyres.id')
                     ->on('ot.showroom_id', '=', 'showrooms.id');
            })

            ->select(
                'tyres.id as tyre_id',
                'tyres.tyre_size',
                'tyres.pattern',
                'tyres.price',
                'showrooms.id as showroom_id',
                'showrooms.name as showroom',
                DB::raw('GREATEST(0, COALESCE(p.purchased,0) - COALESCE(s.sold,0) + COALESCE(it.transferred_in,0) - COALESCE(ot.transferred_out,0)) as stock')
            )

            ->orderBy('showrooms.name')
            ->get()

            ->groupBy('tyre_id');
    }
}
