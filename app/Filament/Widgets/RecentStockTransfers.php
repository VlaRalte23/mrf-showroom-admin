<?php

namespace App\Filament\Widgets;

use App\Models\StockTransfer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentStockTransfers extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StockTransfer::query()
                    ->with(['fromShowroom', 'toShowroom', 'tyre'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tyre.tyre_size')
                    ->label('Tyre')
                    ->description(fn (StockTransfer $record): string => $record->tyre->pattern)
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('fromShowroom.name')
                    ->label('From Showroom')
                    ->icon('heroicon-o-arrow-right')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('toShowroom.name')
                    ->label('To Showroom')
                    ->icon('heroicon-o-arrow-left')
                    ->color('success'),

                Tables\Columns\TextColumn::make('transfer_value')
                    ->label('Value')
                    ->getStateUsing(fn (StockTransfer $record): float => $record->quantity * $record->tyre->price)
                    ->money('INR'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No transfers yet')
            ->emptyStateDescription('Stock transfers will appear here once they are recorded.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }
}