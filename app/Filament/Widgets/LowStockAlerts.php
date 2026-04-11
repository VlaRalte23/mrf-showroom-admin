<?php

namespace App\Filament\Widgets;

use App\Models\Tyre;
use App\Models\Showroom;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Collection;

class LowStockAlerts extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Tyre::query())
            ->columns([
                Tables\Columns\TextColumn::make('tyre_size')
                    ->label('Tyre Size')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pattern')
                    ->label('Pattern')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge(),

                Tables\Columns\TextColumn::make('low_stock_showrooms')
                    ->label('Low Stock Locations')
                    ->getStateUsing(function (Tyre $record) {
                        $showrooms = Showroom::all();
                        $lowStockLocations = [];

                        foreach ($showrooms as $showroom) {
                            $stock = $record->getStockByShowroom($showroom->id);
                            if ($stock <= 10) {
                                $lowStockLocations[] = "{$showroom->name}: {$stock}";
                            }
                        }

                        return implode(', ', $lowStockLocations);
                    })
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('INR')
                    ->sortable(),
            ])
            ->modifyQueryUsing(function ($query) {
                // Filter tyres that have low stock in at least one showroom
                $tyreIds = [];
                $tyres = Tyre::all();
                $showrooms = Showroom::all();

                foreach ($tyres as $tyre) {
                    foreach ($showrooms as $showroom) {
                        $stock = $tyre->getStockByShowroom($showroom->id);
                        if ($stock <= 10) {
                            $tyreIds[] = $tyre->id;
                            break; // Found low stock, no need to check other showrooms
                        }
                    }
                }

                return $query->whereIn('id', array_unique($tyreIds));
            })
            ->emptyStateHeading('No low stock alerts!')
            ->emptyStateDescription('All tyres are sufficiently stocked.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}