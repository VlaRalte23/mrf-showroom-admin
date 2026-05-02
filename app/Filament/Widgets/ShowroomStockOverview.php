<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ShowroomStockOverview extends BaseWidget
{
    protected ?string $heading = 'Stock by Showroom';

    protected function getStats(): array
    {
        $showrooms = DB::table('showrooms')->orderBy('name')->get();

        $purchases = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select('invoice_items.tyre_id', 'invoices.showroom_id', DB::raw('SUM(invoice_items.quantity) as total'))
            ->groupBy('invoice_items.tyre_id', 'invoices.showroom_id')
            ->get()
            ->groupBy('showroom_id')
            ->map(fn ($rows) => $rows->pluck('total', 'tyre_id'));

        $sold = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->select('sale_items.tyre_id', 'sales.showroom_id', DB::raw('SUM(sale_items.quantity) as total'))
            ->groupBy('sale_items.tyre_id', 'sales.showroom_id')
            ->get()
            ->groupBy('showroom_id')
            ->map(fn ($rows) => $rows->pluck('total', 'tyre_id'));

        $inbound = DB::table('stock_transfers')
            ->select('tyre_id', 'to_showroom_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('tyre_id', 'to_showroom_id')
            ->get()
            ->groupBy('to_showroom_id')
            ->map(fn ($rows) => $rows->pluck('total', 'tyre_id'));

        $outbound = DB::table('stock_transfers')
            ->select('tyre_id', 'from_showroom_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('tyre_id', 'from_showroom_id')
            ->get()
            ->groupBy('from_showroom_id')
            ->map(fn ($rows) => $rows->pluck('total', 'tyre_id'));

        $tyreIds = DB::table('tyres')->pluck('id');

        $stats = [];

        foreach ($showrooms as $showroom) {
            $id = $showroom->id;
            $stock = 0;

            foreach ($tyreIds as $tyreId) {
                $tyreStock = ($purchases->get($id)?->get($tyreId, 0) ?? 0)
                    - ($sold->get($id)?->get($tyreId, 0) ?? 0)
                    + ($inbound->get($id)?->get($tyreId, 0) ?? 0)
                    - ($outbound->get($id)?->get($tyreId, 0) ?? 0);

                $stock += max(0, $tyreStock);
            }

            $stats[] = Stat::make($showroom->name, $stock . ' units')
                ->description('Current stock')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color($stock > 0 ? 'success' : 'danger');
        }

        return $stats;
    }
}
