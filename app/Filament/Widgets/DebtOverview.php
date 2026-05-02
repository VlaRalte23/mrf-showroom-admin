<?php

namespace App\Filament\Widgets;

use App\Models\Debt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalDebts = Debt::count();
        $totalDebtAmount = Debt::sum('amount');
        $totalPaidAmount = Debt::sum('paid_amount');
        $totalRemainingAmount = Debt::sum('remaining_amount');
        $unpaidDebts = Debt::where('status', 'unpaid')->count();
        $paidDebts = Debt::where('status', 'paid')->count();

        return [
            Stat::make('Total Debts', $totalDebts)
                ->description('Active debt records')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Total Debt Amount', '₹' . number_format($totalDebtAmount, 2))
                ->description('Total amount owed')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Remaining Amount', '₹' . number_format($totalRemainingAmount, 2))
                ->description('Amount still to be paid')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalRemainingAmount > 0 ? 'danger' : 'success'),

            Stat::make('Unpaid Debts', $unpaidDebts)
                ->description('Debts not fully paid')
                ->descriptionIcon('heroicon-m-clock')
                ->color($unpaidDebts > 0 ? 'danger' : 'success'),
        ];
    }
}