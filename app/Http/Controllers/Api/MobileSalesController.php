<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileSalesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $showroomId = $request->integer('showroom_id');
        $fromDate = trim((string) $request->query('from_date', ''));
        $toDate = trim((string) $request->query('to_date', ''));
        $search = strtolower(trim((string) $request->query('search', '')));

        $sales = Sale::query()
            ->with(['showroom', 'items.tyre'])
            ->when($showroomId > 0, fn ($query) => $query->where('showroom_id', $showroomId))
            ->when($fromDate !== '', fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate !== '', fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->filter(function (Sale $sale) use ($search) {
                if ($search === '') {
                    return true;
                }

                $itemNames = $sale->items
                    ->map(fn ($item) => trim((string) ($item->tyre?->tyre_size ?? '') . ' ' . (string) ($item->tyre?->pattern ?? '')))
                    ->implode(' ');

                $haystack = strtolower(trim(implode(' ', [
                    $sale->showroom?->name,
                    optional($sale->date)?->toDateString(),
                    $sale->notes,
                    $itemNames,
                ])));

                return str_contains($haystack, $search);
            })
            ->values();

        $data = $sales->map(function (Sale $sale) {
            $totalQuantity = (int) $sale->items->sum('quantity');
            $totalAmount = (float) $sale->items->sum(function ($item) {
                return ((float) $item->price) * ((int) $item->quantity);
            });

            $groupedItems = $sale->items
                ->groupBy(function ($item) {
                    $size = $item->tyre?->tyre_size ?? 'Unknown Tyre';
                    $pattern = $item->tyre?->pattern;

                    return trim($size . ' ' . ($pattern ?? ''));
                })
                ->map(function ($items, $name) {
                    return [
                        'name' => (string) $name,
                        'quantity' => (int) $items->sum('quantity'),
                        'amount' => (float) $items->sum(fn ($item) => ((float) $item->price) * ((int) $item->quantity)),
                    ];
                })
                ->values();

            return [
                'id' => (int) $sale->id,
                'date' => optional($sale->date)?->toDateString(),
                'showroom' => [
                    'id' => (int) ($sale->showroom?->id ?? 0),
                    'name' => (string) ($sale->showroom?->name ?? 'Unknown Showroom'),
                ],
                'notes' => $sale->notes,
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
                'items' => $groupedItems,
                'items_summary' => $groupedItems
                    ->map(fn ($item) => $item['name'] . ' = ' . $item['quantity'])
                    ->implode(', '),
            ];
        });

        return response()->json([
            'filters' => [
                'showroom_id' => $showroomId > 0 ? $showroomId : null,
                'from_date' => $fromDate !== '' ? $fromDate : null,
                'to_date' => $toDate !== '' ? $toDate : null,
                'search' => (string) $request->query('search', ''),
            ],
            'summary' => [
                'total_sales' => $data->count(),
                'total_quantity' => (int) $data->sum('total_quantity'),
                'total_amount' => (float) $data->sum('total_amount'),
            ],
            'data' => $data->values(),
        ]);
    }
}
