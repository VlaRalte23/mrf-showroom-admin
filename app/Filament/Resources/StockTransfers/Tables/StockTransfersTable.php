<?php

namespace App\Filament\Resources\StockTransfers\Tables;

use App\Models\StockTransfer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class StockTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $groupExpression = "CASE
                    WHEN st.batch_id IS NOT NULL AND st.batch_id NOT LIKE 'legacy-%'
                    THEN st.batch_id
                    ELSE CONCAT(
                        'legacygrp-',
                        st.from_showroom_id,
                        '-',
                        st.to_showroom_id,
                        '-',
                        DATE_FORMAT(st.date, '%Y%m%d'),
                        '-',
                        COALESCE(st.notes, ''),
                        '-',
                        DATE_FORMAT(st.created_at, '%Y-%m-%d %H:%i:%s')
                    )
                END";

                $query->whereIn('stock_transfers.id', function ($subQuery) use ($groupExpression) {
                    $subQuery
                        ->from('stock_transfers as st')
                        ->selectRaw('MIN(st.id)')
                        ->groupByRaw($groupExpression);
                });
            })
            ->columns([

                TextColumn::make('fromShowroom.name')
                    ->label('From Showroom')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toShowroom.name')
                    ->label('To Showroom')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tyres')
                    ->label('Tyres')
                    ->wrap()
                    ->getStateUsing(function (StockTransfer $record): string {
                        $batchTransfers = self::getBatchTransfersQuery($record)
                            ->with('tyre')
                            ->get();

                        return $batchTransfers
                            ->groupBy(function (StockTransfer $transfer) {
                                $size = $transfer->tyre?->tyre_size ?? 'Unknown Tyre';
                                $pattern = $transfer->tyre?->pattern;

                                return trim($size . ' ' . ($pattern ?? ''));
                            })
                            ->map(fn ($items, $name) => $name . ' = ' . $items->sum('quantity'))
                            ->implode(', ');
                    }),

                TextColumn::make('quantity')
                    ->label('Total Quantity')
                    ->sortable()
                    ->getStateUsing(function (StockTransfer $record): int {
                        return (int) self::getBatchTransfersQuery($record)
                            ->sum('quantity');
                    }),

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
                EditAction::make()
                    ->label('Edit First Row'),
            ])

            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getBatchTransfersQuery(StockTransfer $record): Builder
    {
        if (filled($record->batch_id) && !str_starts_with((string) $record->batch_id, 'legacy-')) {
            return StockTransfer::query()->where('batch_id', $record->batch_id);
        }

        $createdAt = Carbon::parse($record->created_at);

        return StockTransfer::query()
            ->where('from_showroom_id', $record->from_showroom_id)
            ->where('to_showroom_id', $record->to_showroom_id)
            ->whereDate('date', $record->date)
            ->when(
                filled($record->notes),
                fn (Builder $query) => $query->where('notes', $record->notes),
                fn (Builder $query) => $query->whereNull('notes')
            )
            ->whereBetween('created_at', [$createdAt->copy()->startOfSecond(), $createdAt->copy()->endOfSecond()]);
    }
}
