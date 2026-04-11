<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\Showroom;
use Filament\Widgets\ChartWidget;

class MonthlySalesTrend extends ChartWidget
{
    protected ?string $heading = 'Monthly Sales Trend by Showroom';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $datasets = [];
        $months = [];
        $colors = [
            'rgb(75, 192, 192)',   // Teal
            'rgb(255, 99, 132)',   // Red
            'rgb(54, 162, 235)',   // Blue
            'rgb(255, 205, 86)',   // Yellow
            'rgb(153, 102, 255)',  // Purple
            'rgb(255, 159, 64)',   // Orange
        ];

        $showrooms = Showroom::all();

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
        }

        foreach ($showrooms as $index => $showroom) {
            $data = [];

            // Calculate sales for each month for this showroom
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $monthlySales = Sale::where('showroom_id', $showroom->id)
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->with('items')
                    ->get()
                    ->sum(function ($sale) {
                        return $sale->items->sum(function ($item) {
                            return $item->quantity * $item->price;
                        });
                    });

                // Ensure no negative values - only show positive sales
                $monthlySales = max(0, $monthlySales);
                $data[] = $monthlySales;
            }

            $color = $colors[$index % count($colors)];

            $datasets[] = [
                'label' => $showroom->name,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => str_replace('rgb', 'rgba', $color) . ', 0.2)',
                'tension' => 0.4,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'min' => 0,
                    'ticks' => [
                        'callback' => 'function(value) { return value >= 0 ? "₹" + value.toLocaleString() : ""; }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}