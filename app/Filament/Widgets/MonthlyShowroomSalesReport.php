<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Showroom;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MonthlyShowroomSalesReport extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        $monthlySalesSubquery = SaleItem::query()
            ->selectRaw('COALESCE(SUM(sale_items.quantity * sale_items.price), 0)')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereColumn('sales.showroom_id', 'showrooms.id')
            ->whereYear('sales.date', now()->year)
            ->whereMonth('sales.date', now()->month);

        $monthlyItemsSubquery = SaleItem::query()
            ->selectRaw('COALESCE(SUM(sale_items.quantity), 0)')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereColumn('sales.showroom_id', 'showrooms.id')
            ->whereYear('sales.date', now()->year)
            ->whereMonth('sales.date', now()->month);

        $monthlyBillsSubquery = Sale::query()
            ->selectRaw('COUNT(*)')
            ->whereColumn('sales.showroom_id', 'showrooms.id')
            ->whereYear('sales.date', now()->year)
            ->whereMonth('sales.date', now()->month);

        return $table
            ->query(
                Showroom::query()
                    ->select('showrooms.*')
                    ->selectSub($monthlySalesSubquery, 'monthly_sales')
                    ->selectSub($monthlyItemsSubquery, 'items_sold')
                    ->selectSub($monthlyBillsSubquery, 'bills_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Showroom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('report_month')
                    ->label('Month')
                    ->state(fn (): string => now()->format('F Y')),

                Tables\Columns\TextColumn::make('monthly_sales')
                    ->label('Monthly Sales (INR)')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => '₹' . number_format((float) $state, 2))
                    ->weight(fn ($state): string => ((float) $state) > 0 ? 'bold' : 'normal')
                    ->color(fn ($state): string => ((float) $state) > 0 ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('bills_count')
                    ->label('Bills')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_sold')
                    ->label('Items Sold')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->defaultSort('monthly_sales', 'desc')
            ->emptyStateHeading('No sales in this month yet')
            ->emptyStateDescription('Showroom monthly sales will appear here once sales are added.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }

    public function getHeading(): string
    {
        return 'Monthly Sales Report - ' . now()->format('F Y');
    }
}
