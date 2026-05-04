<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileStockReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = strtolower(trim((string) $request->query('search', '')));
        $categoryFilter = trim((string) $request->query('category', ''));

        $showrooms = DB::table('showrooms')
            ->orderBy('name')
            ->get(['id', 'name']);

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

        $incomingTransfers = DB::table('stock_transfers')
            ->select(
                'tyre_id',
                'to_showroom_id as showroom_id',
                DB::raw('SUM(quantity) as transferred_in')
            )
            ->groupBy('tyre_id', 'to_showroom_id');

        $outgoingTransfers = DB::table('stock_transfers')
            ->select(
                'tyre_id',
                'from_showroom_id as showroom_id',
                DB::raw('SUM(quantity) as transferred_out')
            )
            ->groupBy('tyre_id', 'from_showroom_id');

        $rows = DB::table('tyres')
            ->crossJoin('showrooms')
            ->leftJoinSub($purchases, 'p', function ($join) {
                $join->on('p.tyre_id', '=', 'tyres.id')
                    ->on('p.showroom_id', '=', 'showrooms.id');
            })
            ->leftJoinSub($sales, 's', function ($join) {
                $join->on('s.tyre_id', '=', 'tyres.id')
                    ->on('s.showroom_id', '=', 'showrooms.id');
            })
            ->leftJoinSub($incomingTransfers, 'it', function ($join) {
                $join->on('it.tyre_id', '=', 'tyres.id')
                    ->on('it.showroom_id', '=', 'showrooms.id');
            })
            ->leftJoinSub($outgoingTransfers, 'ot', function ($join) {
                $join->on('ot.tyre_id', '=', 'tyres.id')
                    ->on('ot.showroom_id', '=', 'showrooms.id');
            })
            ->select(
                'tyres.id as tyre_id',
                'tyres.tyre_size',
                'tyres.pattern',
                'tyres.category',
                'tyres.price',
                'showrooms.id as showroom_id',
                'showrooms.name as showroom_name',
                DB::raw('GREATEST(0, COALESCE(p.purchased,0) - COALESCE(s.sold,0) + COALESCE(it.transferred_in,0) - COALESCE(ot.transferred_out,0)) as stock')
            )
            ->orderByRaw('COALESCE(tyres.category, "")')
            ->orderBy('tyres.tyre_size')
            ->orderBy('showrooms.name')
            ->get();

        $stockGroups = $rows
            ->groupBy(function ($row) {
                return trim((string) ($row->category ?? '')) !== '' ? $row->category : 'No Category';
            })
            ->map(function ($categoryRows) {
                return $categoryRows->groupBy('tyre_id');
            });

        $filteredGroups = collect($stockGroups)
            ->when($categoryFilter !== '', function ($groups) use ($categoryFilter) {
                return $groups->filter(fn ($items, $category) => $category === $categoryFilter);
            })
            ->map(function ($tyres) use ($search) {
                return $tyres->filter(function ($items) use ($search) {
                    if ($search === '') {
                        return true;
                    }

                    $first = $items->first();
                    $label = strtolower(trim(($first->tyre_size ?? '') . ' ' . ($first->pattern ?? '') . ' ' . ($first->category ?? '')));

                    return str_contains($label, $search);
                });
            })
            ->filter(fn ($tyres) => $tyres->isNotEmpty());

        $categories = [];

        foreach ($filteredGroups as $categoryName => $tyres) {
            $tyreRows = [];

            foreach ($tyres as $items) {
                $first = $items->first();
                $stockByShowroom = [];

                foreach ($items as $item) {
                    $stockByShowroom[(string) $item->showroom_id] = (int) $item->stock;
                }

                $tyreRows[] = [
                    'tyre_id' => (int) $first->tyre_id,
                    'tyre_size' => (string) $first->tyre_size,
                    'pattern' => $first->pattern,
                    'price' => (float) $first->price,
                    'stock_by_showroom' => $stockByShowroom,
                    'total_stock' => array_sum($stockByShowroom),
                ];
            }

            $categories[] = [
                'category' => (string) $categoryName,
                'tyres' => $tyreRows,
            ];
        }

        return response()->json([
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'category' => $categoryFilter,
            ],
            'showrooms' => $showrooms->map(fn ($showroom) => [
                'id' => (int) $showroom->id,
                'name' => (string) $showroom->name,
            ])->values(),
            'category_options' => $stockGroups->keys()->values(),
            'category_counts' => $stockGroups->map(fn ($tyres) => $tyres->count()),
            'data' => $categories,
        ]);
    }
}
