<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // Showroom
                TextColumn::make('showroom.name')
                    ->label('Showroom')
                    ->searchable(),

                // Tyres Sold (size + qty)
                TextColumn::make('items.tyre.tyre_size')
                    ->label('Tyres Sold')
                    ->formatStateUsing(function ($record) {
                        return $record->items
                            ->map(function ($item) {
                                return $item->tyre->tyre_size . ' (' . $item->quantity . ')';
                            })
                            ->implode(', ');
                    }),

                // Total Quantity
                TextColumn::make('total_quantity')
                    ->label('Total Qty')
                    ->getStateUsing(function ($record) {
                        return $record->items->sum('quantity');
                    }),

                // Total Price 💰
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->getStateUsing(function ($record) {
                        return $record->items->sum(function ($item) {
                            return $item->quantity * $item->price;
                        });
                    })
                    ->money('INR'),

                // Date
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                // Notes
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

            ])

            ->defaultSort('date', 'desc')

            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('From Date'),
                        DatePicker::make('date_to')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn ($query) => $query->whereDate('date', '>=', $data['date_from'])
                            )
                            ->when(
                                $data['date_to'],
                                fn ($query) => $query->whereDate('date', '<=', $data['date_to'])
                            );
                    }),
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}