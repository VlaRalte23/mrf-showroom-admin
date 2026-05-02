<?php

namespace App\Filament\Resources\Debts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DebtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('sale.showroom.name')
                    ->label('Showroom')
                    ->sortable(),

                TextColumn::make('items_bought')
                    ->label('Items Bought')
                    ->getStateUsing(function ($record) {
                        if (!$record->sale) {
                            return '-';
                        }

                        $groupedItems = $record->sale->items
                            ->groupBy(function ($item) {
                                $size = $item->tyre?->tyre_size ?? 'Unknown Tyre';
                                $pattern = $item->tyre?->pattern;

                                return trim($size . ' ' . ($pattern ?? ''));
                            })
                            ->map(function ($items, $name) {
                                return $name . ' = ' . $items->sum('quantity');
                            });

                        return $groupedItems->implode(', ');
                    })
                    ->wrap(),

                TextColumn::make('amount')
                    ->label('Total Amount')
                    ->money('INR')
                    ->sortable(),

                TextColumn::make('paid_amount')
                    ->label('Paid Amount')
                    ->money('INR')
                    ->sortable(),

                TextColumn::make('remaining_amount')
                    ->label('Remaining')
                    ->money('INR')
                    ->sortable()
                    ->color(fn ($record) => $record->remaining_amount > 0 ? 'danger' : 'success'),

                TextColumn::make('paid_date')
                    ->label('Paid Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->paid_date ? 'success' : null),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'unpaid',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-x-circle' => 'unpaid',
                    ]),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}