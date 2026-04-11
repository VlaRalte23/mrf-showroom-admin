<?php

namespace App\Filament\Resources\StockTransfers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class StockTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('fromShowroom.name')
                    ->label('From Showroom')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toShowroom.name')
                    ->label('To Showroom')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tyre.tyre_size')
                    ->label('Tyre')
                    ->formatStateUsing(function ($record) {
                        return $record->tyre->tyre_size . ' ' . $record->tyre->pattern;
                    }),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

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

            ->filters([
                //
            ])

            ->defaultSort('date', 'desc')

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
