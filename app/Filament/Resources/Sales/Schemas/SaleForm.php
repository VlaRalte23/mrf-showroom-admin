<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Showroom;
use App\Models\Tyre;
use App\Support\SpaceInsensitiveSearch;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SaleForm
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
                ->required()
                ->reactive(),

            DatePicker::make('date')
                ->default(today())
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->placeholder('Add notes about debt, payment status, or other details...')
                ->rows(3),

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
                        ->required()
                        ->reactive(),

                    TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->reactive()
                        ->helperText(function ($state, $get) {
                            $tyreId = $get('tyre_id');
                            $showroomId = $get('../../showroom_id');

                            if (!$tyreId || !$showroomId) {
                                return 'Select a showroom and tyre to see available stock.';
                            }

                            $tyre = Tyre::find($tyreId);

                            if (!$tyre) {
                                return 'Tyre not found.';
                            }

                            $stock = $tyre->getStockByShowroom($showroomId);

                            return "Available stock: {$stock}";
                        })
                        ->rule(function ($get) {

                            return function ($attribute, $value, $fail) use ($get) {

                                $tyreId = $get('tyre_id');

                                // get showroom from parent form
                                $showroomId = $get('../../showroom_id');

                                if (!$tyreId || !$showroomId) {
                                    return;
                                }

                                $tyre = Tyre::find($tyreId);

                                if (!$tyre) {
                                    return;
                                }

                                $stock = $tyre->getStockByShowroom($showroomId);

                                if ($value > $stock) {
                                    $fail("Only {$stock} tyres available in stock.");
                                }

                            };

                        }),
                        TextInput::make('price')
            ->label('Price')
            ->numeric()
            ->minValue(0)
            ->prefix('₹')
            ->required(),


                ])
                ->columns(2)

        ]);
    }
}