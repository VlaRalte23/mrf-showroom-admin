<?php

namespace App\Filament\Resources\Debts\Schemas;

use App\Support\SpaceInsensitiveSearch;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class DebtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // Sale Information
            Select::make('sale_id')
                ->label('Sale')
                ->options(
                    Sale::with('showroom')
                        ->get()
                        ->mapWithKeys(function ($sale) {
                            $formattedDate = $sale->date instanceof \DateTimeInterface
                                ? $sale->date->format('d/m/Y')
                                : Carbon::parse($sale->date)->format('d/m/Y');

                            $showroomName = $sale->showroom?->name ?? 'Unknown Showroom';

                            return [$sale->id => "Sale #{$sale->id} - {$showroomName} - {$formattedDate}"];
                        })
                        ->map(fn ($label) => filled($label) ? (string) $label : 'Unknown Sale')
                        ->all()
                )
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    $normalizedSearch = SpaceInsensitiveSearch::normalize($search);

                    return Sale::query()
                        ->with('showroom')
                        ->where(function ($query) use ($normalizedSearch) {
                            $query->whereRaw(SpaceInsensitiveSearch::sqlCompactExpression('CAST(id as CHAR)') . ' LIKE ?', ["%{$normalizedSearch}%"])
                                ->orWhereRaw(SpaceInsensitiveSearch::sqlCompactExpression("DATE_FORMAT(date, '%d/%m/%Y')") . ' LIKE ?', ["%{$normalizedSearch}%"])
                                ->orWhereHas('showroom', function ($showroomQuery) use ($normalizedSearch): void {
                                    $showroomQuery->whereRaw(SpaceInsensitiveSearch::sqlCompactExpression('name') . ' LIKE ?', ["%{$normalizedSearch}%"]);
                                });
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(function (Sale $sale) {
                            $formattedDate = $sale->date instanceof \DateTimeInterface
                                ? $sale->date->format('d/m/Y')
                                : Carbon::parse($sale->date)->format('d/m/Y');

                            $showroomName = $sale->showroom?->name ?? 'Unknown Showroom';

                            return [$sale->id => "Sale #{$sale->id} - {$showroomName} - {$formattedDate}"];
                        })
                        ->map(fn ($label) => filled($label) ? (string) $label : 'Unknown Sale')
                        ->all();
                })
                ->getOptionLabelUsing(function ($value): ?string {
                    $sale = Sale::with('showroom')->find($value);

                    if (! $sale) {
                        return null;
                    }

                    $formattedDate = $sale->date instanceof \DateTimeInterface
                        ? $sale->date->format('d/m/Y')
                        : Carbon::parse($sale->date)->format('d/m/Y');

                    $showroomName = $sale->showroom?->name ?? 'Unknown Showroom';

                    return "Sale #{$sale->id} - {$showroomName} - {$formattedDate}";
                })
                ->default(fn () => request()->integer('sale_id') ?: null)
                ->disabled(fn () => filled(request()->query('sale_id')))
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $saleId = is_numeric($state) ? (int) $state : null;
                        $sale = $saleId ? Sale::with('items')->find($saleId) : null;

                        if ($sale instanceof Sale) {
                            $total = $sale->items->sum(function ($item) {
                                return $item->quantity * $item->price;
                            });
                            $set('amount', $total);
                            $set('remaining_amount', $total);
                        }
                    }
                }),

            // Customer Information
            TextInput::make('customer_name')
                ->label('Customer Name')
                ->required()
                ->maxLength(255),

            TextInput::make('customer_phone')
                ->label('Customer Phone')
                ->tel()
                ->maxLength(20),

            // Debt Details
            TextInput::make('amount')
                ->label('Total Amount')
                ->numeric()
                ->prefix('₹')
                ->required()
                ->minValue(0)
                ->live()
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    $paidAmount = $get('paid_amount') ?? 0;
                    $set('remaining_amount', max(0, $state - $paidAmount));
                }),

            TextInput::make('paid_amount')
                ->label('Paid Amount')
                ->numeric()
                ->prefix('₹')
                ->default(0)
                ->minValue(0)
                ->live()
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    $totalAmount = $get('amount') ?? 0;
                    $set('remaining_amount', max(0, $totalAmount - $state));
                }),

            TextInput::make('remaining_amount')
                ->label('Remaining Amount')
                ->numeric()
                ->prefix('₹')
                ->disabled()
                ->dehydrated(false),

            DatePicker::make('paid_date')
                ->label('Paid Date')
                ->default(today()),

            Select::make('status')
                ->label('Status')
                ->options([
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                ])
                ->default('unpaid')
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3),

        ]);
    }
}