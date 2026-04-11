<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSales extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->with(['showroom', 'items.tyre'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('showroom.name')
                    ->label('Showroom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->getStateUsing(fn (Sale $record): int => $record->items->sum('quantity'))
                    ->badge(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->getStateUsing(function (Sale $record): float {
                        return $record->items->sum(function ($item) {
                            return $item->quantity * $item->price;
                        });
                    })
                    ->money('INR'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->placeholder('No notes')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('No sales yet')
            ->emptyStateDescription('Sales will appear here once they are recorded.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}