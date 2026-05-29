<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Support\SpaceInsensitiveSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDebtController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = trim((string) $request->query('status', ''));
        $showroomId = $request->integer('showroom_id');
        $search = SpaceInsensitiveSearch::normalize((string) $request->query('search', ''));

        $debts = Debt::query()
            ->with(['sale.showroom', 'sale.items.tyre'])
            ->when(in_array($status, ['paid', 'unpaid'], true), fn ($query) => $query->where('status', $status))
            ->when($showroomId > 0, fn ($query) => $query->whereHas('sale', fn ($sale) => $sale->where('showroom_id', $showroomId)))
            ->orderByDesc('id')
            ->get()
            ->filter(function (Debt $debt) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystack = strtolower(trim(implode(' ', [
                    $debt->customer_name,
                    $debt->customer_phone,
                    $debt->sale?->showroom?->name,
                ])));

                return str_contains(SpaceInsensitiveSearch::normalize($haystack), $search);
            })
            ->values();

        $data = $debts->map(function (Debt $debt) {
            $groupedItems = $debt->sale?->items
                ?->groupBy(function ($item) {
                    $size = $item->tyre?->tyre_size ?? 'Unknown Tyre';
                    $pattern = $item->tyre?->pattern;

                    return trim($size . ' ' . ($pattern ?? ''));
                })
                ->map(function ($items, $name) {
                    return [
                        'name' => (string) $name,
                        'quantity' => (int) $items->sum('quantity'),
                    ];
                })
                ->values() ?? collect();

            return [
                'id' => (int) $debt->id,
                'customer_name' => (string) $debt->customer_name,
                'customer_phone' => $debt->customer_phone,
                'amount' => (float) $debt->amount,
                'paid_amount' => (float) $debt->paid_amount,
                'remaining_amount' => (float) $debt->remaining_amount,
                'paid_date' => optional($debt->paid_date)?->toDateString(),
                'status' => (string) $debt->status,
                'notes' => $debt->notes,
                'sale' => [
                    'id' => (int) ($debt->sale?->id ?? 0),
                    'showroom_id' => (int) ($debt->sale?->showroom_id ?? 0),
                    'showroom_name' => (string) ($debt->sale?->showroom?->name ?? 'Unknown Showroom'),
                    'date' => optional($debt->sale?->date)?->toDateString(),
                ],
                'items_bought' => $groupedItems,
                'items_bought_summary' => $groupedItems
                    ->map(fn ($item) => $item['name'] . ' = ' . $item['quantity'])
                    ->implode(', '),
            ];
        });

        return response()->json([
            'filters' => [
                'status' => $status,
                'showroom_id' => $showroomId > 0 ? $showroomId : null,
                'search' => (string) $request->query('search', ''),
            ],
            'summary' => [
                'total_debts' => $data->count(),
                'paid_count' => $data->where('status', 'paid')->count(),
                'unpaid_count' => $data->where('status', 'unpaid')->count(),
                'total_amount' => (float) $data->sum('amount'),
                'total_paid_amount' => (float) $data->sum('paid_amount'),
                'total_remaining_amount' => (float) $data->sum('remaining_amount'),
            ],
            'data' => $data->values(),
        ]);
    }
}
