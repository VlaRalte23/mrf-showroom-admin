<?php

namespace App\Filament\Widgets;

use App\Models\Showroom;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class DailySalesReport extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        $today = today();
        $dailySalesTotal = $this->getDailySalesTotal($today);

        $query = Showroom::query()
            ->select('showrooms.id', 'showrooms.name', 'showrooms.created_at', 'showrooms.updated_at')
            ->selectSub(function ($subQuery) use ($today) {
                $subQuery
                    ->from('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->whereColumn('sales.showroom_id', 'showrooms.id')
                    ->whereDate('sales.date', $today)
                    ->selectRaw('COALESCE(SUM(sale_items.quantity * sale_items.price), 0)');
            }, 'daily_sales')
            ->selectSub(function ($subQuery) use ($today) {
                $subQuery
                    ->from('sales')
                    ->whereColumn('sales.showroom_id', 'showrooms.id')
                    ->whereDate('sales.date', $today)
                    ->selectRaw('COALESCE(COUNT(*), 0)');
            }, 'bills_count')
            ->selectSub(function ($subQuery) use ($today) {
                $subQuery
                    ->from('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->whereColumn('sales.showroom_id', 'showrooms.id')
                    ->whereDate('sales.date', $today)
                    ->selectRaw('COALESCE(SUM(sale_items.quantity), 0)');
            }, 'items_sold');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Showroom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('daily_sales')
                    ->label('Today\'s Sales (INR)')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => '₹' . number_format((float) $state, 2))
                    ->weight(fn ($record): string => ((float) $record->daily_sales) > 0 ? 'bold' : 'normal')
                    ->color(fn ($record): string => ((float) $record->daily_sales) > 0 ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('bills_count')
                    ->label('Bills Today')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_sold')
                    ->label('Items Sold Today')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->defaultKeySort(false)
            ->defaultSort('daily_sales', 'desc')
                ->description('Total today sales: ₹' . number_format($dailySalesTotal, 2))
            ->emptyStateHeading('No sales today')
            ->emptyStateDescription('Sales for today will appear here once they are recorded.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }

            protected function getDailySalesTotal($date): float
            {
            return (float) DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->whereDate('sales.date', $date)
                ->sum(DB::raw('sale_items.quantity * sale_items.price'));
            }

    public function getHeading(): string
    {
        return 'Daily Sales Report - ' . now()->format('l, F j, Y');
    }
}
