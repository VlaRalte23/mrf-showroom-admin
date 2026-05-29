<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Support\SpaceInsensitiveSearch;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use App\Models\Showroom;
use App\Models\Tyre;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('showroom_id')
                ->label('Showroom')
                ->options(
                    Showroom::query()
                        ->pluck('name', 'id')
                        ->map(fn ($label) => filled($label) ? (string) $label : 'Unknown Showroom')
                        ->all()
                )
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    $normalizedSearch = SpaceInsensitiveSearch::normalize($search);

                    return Showroom::query()
                        ->whereRaw(SpaceInsensitiveSearch::sqlCompactExpression('name') . ' LIKE ?', ["%{$normalizedSearch}%"])
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->map(fn ($label) => filled($label) ? (string) $label : 'Unknown Showroom')
                        ->all();
                })
                ->getOptionLabelUsing(fn ($value): ?string => Showroom::find($value)?->name)
                ->required(),

            TextInput::make('invoice_no')
                ->label('Invoice Number')
                ->required(),

            DatePicker::make('date')
                ->default(today())
                ->required(),

            Repeater::make('items')
                ->relationship()
                ->schema([

                    Select::make('tyre_id')
                        ->label('Tyre')
                        ->options(
                            Tyre::query()
                                ->select('id')
                                ->selectRaw("TRIM(CONCAT_WS(' ', COALESCE(tyre_size, ''), COALESCE(pattern, ''))) as label")
                                ->pluck('label', 'id')
                                ->map(fn ($label) => filled($label) ? (string) $label : 'Unknown Tyre')
                                ->all()
                        )
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search): array {
                            $normalizedSearch = SpaceInsensitiveSearch::normalize($search);

                            return Tyre::query()
                                ->select('id')
                                ->selectRaw("TRIM(CONCAT_WS(' ', COALESCE(tyre_size, ''), COALESCE(pattern, ''))) as label")
                                ->whereRaw(SpaceInsensitiveSearch::sqlCompactExpression("CONCAT_WS(' ', COALESCE(tyre_size, ''), COALESCE(pattern, ''))") . ' LIKE ?', ["%{$normalizedSearch}%"])
                                ->limit(50)
                                ->pluck('label', 'id')
                                ->map(fn ($label) => filled($label) ? (string) $label : 'Unknown Tyre')
                                ->all();
                        })
                        ->getOptionLabelUsing(function ($value): ?string {
                            $tyre = Tyre::find($value);

                            if (! $tyre) {
                                return null;
                            }

                            return trim(($tyre->tyre_size ?? '') . ' ' . ($tyre->pattern ?? ''));
                        })
                        ->required(),

                    TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    TextInput::make('price')
                        ->numeric()
                        ->minValue(0)
                        ->label('Cost Price'),

                ])
                ->columns(3)

        ]);
    }
}