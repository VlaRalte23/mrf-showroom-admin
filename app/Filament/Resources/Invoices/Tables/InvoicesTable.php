<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Support\SpaceInsensitiveSearch;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('invoice_no')
                    ->label('Invoice Number')
                    ->searchable(query: fn (Builder $query, string $search): Builder => SpaceInsensitiveSearch::whereColumn($query, 'invoice_no', $search)),

                TextColumn::make('showroom.name')
                    ->label('Showroom')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('showroom', function (Builder $showroomQuery) use ($search): void {
                            SpaceInsensitiveSearch::whereColumn($showroomQuery, 'name', $search);
                        });
                    }),

                TextColumn::make('date')
                    ->label('Invoice Date')
                    ->date(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])

            ->filters([
                //
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