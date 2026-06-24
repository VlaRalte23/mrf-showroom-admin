<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('attendance_type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'clock_in' => 'Clock In',
                        'clock_out' => 'Clock Out',
                        default => (string) $state,
                    })
                    ->sortable(),

                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->sortable(),

                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->sortable(),

                BooleanColumn::make('is_within_geofence')
                    ->label('Within Geofence')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Recorded At')
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
