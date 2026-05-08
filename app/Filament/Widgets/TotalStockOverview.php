<?php

namespace App\Filament\Widgets;

use App\Models\Tyre;
use App\Models\Showroom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalStockOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTyres = Tyre::count();
        $totalShowrooms = Showroom::count();

        // Calculate total stock across all showrooms
        $totalStock = 0;
        $showrooms = Showroom::all();

        foreach ($showrooms as $showroom) {
            foreach (Tyre::all() as $tyre) {
                $totalStock += $tyre->getStockByShowroom($showroom->id);
            }
        }

        return [
            Stat::make('Total Tyres', $totalTyres)
                ->description('Different tyre types')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Total Showrooms', $totalShowrooms)
                ->description('Active showrooms')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('Total Stock', $totalStock)
                ->description('Tyres in inventory')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('warning'),
        ];
    }
}