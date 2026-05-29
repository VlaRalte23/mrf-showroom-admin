<?php

namespace App\Filament\Resources\Tyres\Tables;

use App\Support\SpaceInsensitiveSearch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class TyresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('tyre_size')
                    ->label('Tyre Size')
                    ->searchable(query: fn (Builder $query, string $search): Builder => SpaceInsensitiveSearch::whereColumn($query, 'tyre_size', $search)),

                TextColumn::make('pattern')
                    ->label('Pattern')
                    ->searchable(query: fn (Builder $query, string $search): Builder => SpaceInsensitiveSearch::whereColumn($query, 'pattern', $search)),

                TextColumn::make('category')
                    ->label('Category'),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('INR')
                    ->sortable(),

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