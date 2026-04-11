<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\Showroom;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesByShowroomChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'Sales by Showroom (Last 30 Days)';
    }

    protected function getData(): array
    {
        $showrooms = Showroom::all();
        $data = [];

        foreach ($showrooms as $showroom) {
            $totalSales = Sale::where('showroom_id', $showroom->id)
                ->where('date', '>=', now()->subDays(30))
                ->with('items')
                ->get()
                ->sum(function ($sale) {
                    return $sale->items->sum(function ($item) {
                        return $item->quantity * $item->price;
                    });
                });

            $data['labels'][] = $showroom->name;
            $data['datasets'][0]['data'][] = $totalSales;
        }

        $data['datasets'][0]['label'] = 'Sales Amount (₹)';
        $data['datasets'][0]['backgroundColor'] = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
        ];
        $data['datasets'][0]['borderColor'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
        ];
        $data['datasets'][0]['borderWidth'] = 1;

        return $data;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "₹" + value.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}