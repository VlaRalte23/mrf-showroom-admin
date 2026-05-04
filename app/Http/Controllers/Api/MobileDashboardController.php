<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MobileDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalTyres = DB::table('tyres')->count();
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

        $showroomStock = [];
        $totalStock = 0;

        foreach ($showrooms as $showroom) {
            $showroomTotal = 0;

            foreach ($tyreIds as $tyreId) {
                $tyreStock = ($purchases->get($showroom->id)?->get($tyreId, 0) ?? 0)
                    - ($sold->get($showroom->id)?->get($tyreId, 0) ?? 0)
                    + ($inbound->get($showroom->id)?->get($tyreId, 0) ?? 0)
                    - ($outbound->get($showroom->id)?->get($tyreId, 0) ?? 0);

                $showroomTotal += max(0, $tyreStock);
            }

            $showroomStock[] = [
                'showroom_id' => $showroom->id,
                'showroom_name' => $showroom->name,
                'stock' => (int) $showroomTotal,
            ];

            $totalStock += $showroomTotal;
        }

        $todaySalesValue = Sale::query()
            ->whereDate('date', today())
            ->with('items')
            ->get()
            ->sum(function ($sale) {
                return $sale->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
            });

        return response()->json([
            'data' => [
                'total_tyres' => (int) $totalTyres,
                'total_showrooms' => (int) $showrooms->count(),
                'total_stock' => (int) $totalStock,
                'daily_sales_value' => (float) $todaySalesValue,
                'showroom_stock' => $showroomStock,
            ],
        ]);
    }
}
