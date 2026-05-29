<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Support\SpaceInsensitiveSearch;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // Showroom
                TextColumn::make('showroom.name')
                    ->label('Showroom')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('showroom', function (Builder $showroomQuery) use ($search): void {
                            SpaceInsensitiveSearch::whereColumn($showroomQuery, 'name', $search);
                        });
                    }),

                // Tyres Sold (size + qty)
                TextColumn::make('tyres_sold_summary')
                    ->label('Tyres Sold')
                    ->getStateUsing(function ($record) {
                        return $record->items
                            ->groupBy(function ($item) {
                                $size = $item->tyre?->tyre_size ?? 'Unknown Tyre';
                                $pattern = $item->tyre?->pattern;

                                return trim($size . ' ' . ($pattern ?? ''));
                            })
                            ->map(function ($items, $name) {
                                return $name . ' (' . $items->sum('quantity') . ')';
                            })
                            ->implode(', ');
                    })
                    ->wrap(),

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
                        if (! filled($state) || mb_strlen((string) $state) <= 50) {
                            return null;
                        }
                        return (string) $state;
                    }),

                // Debt Status
                BadgeColumn::make('debt_status')
                    ->label('Debt')
                    ->getStateUsing(function ($record) {
                        return $record->hasDebt() ? 'Has Debt' : 'No Debt';
                    })
                    ->colors([
                        'success' => 'No Debt',
                        'warning' => 'Has Debt',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'No Debt',
                        'heroicon-o-exclamation-triangle' => 'Has Debt',
                    ]),

            ])

            ->defaultSort('date', 'desc')

            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('From Date')
                            ->default(today()),
                        DatePicker::make('date_to')
                            ->label('To Date')
                            ->default(today()),
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

            ->actions([
                Action::make('create_debt')
                    ->label('Create Debt')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->visible(fn ($record) => !$record->hasDebt())
                    ->url(fn ($record) => route('filament.admin.resources.debts.create', ['sale_id' => $record->id])),
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}