<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use App\Models\Showroom;
use App\Models\StockTransfer;
use App\Models\Tyre;
use App\Support\SpaceInsensitiveSearch;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('from_showroom_id')
                ->label('From Showroom')
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

            Select::make('to_showroom_id')
                ->label('To Showroom')
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

            Repeater::make('transfer_items')
                ->label('Tyres')
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
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->reactive(),

                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->reactive()
                        ->helperText(function ($state, $get) {
                            $tyreId = $get('tyre_id');
                            $fromShowroomId = $get('../../from_showroom_id');

                            if (!$tyreId || !$fromShowroomId) {
                                return 'Select a source showroom and tyre to see available stock.';
                            }

                            $tyre = Tyre::find($tyreId);

                            if (!$tyre) {
                                return 'Tyre not found.';
                            }

                            $stock = $tyre->getStockByShowroom($fromShowroomId);

                            return "Available stock: {$stock}";
                        })
                        ->rule(function ($get) {
                            return function ($attribute, $value, $fail) use ($get) {
                                $tyreId = $get('tyre_id');
                                $fromShowroomId = $get('../../from_showroom_id');

                                if (!$tyreId || !$fromShowroomId) {
                                    return;
                                }

                                $tyre = Tyre::find($tyreId);

                                if (!$tyre) {
                                    return;
                                }

                                $stock = $tyre->getStockByShowroom($fromShowroomId);

                                if ($value > $stock) {
                                    $fail("Only {$stock} tyres available in {$tyre->tyre_size} {$tyre->pattern} at source showroom.");
                                }
                            };
                        }),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->minItems(1)
                ->addActionLabel('Add Tyre')
                ->required()
                ->visible(fn (?StockTransfer $record) => $record === null),

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
                ->required(fn (?StockTransfer $record) => $record !== null)
                ->visible(fn (?StockTransfer $record) => $record !== null)
                ->reactive(),

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->required(fn (?StockTransfer $record) => $record !== null)
                ->visible(fn (?StockTransfer $record) => $record !== null)
                ->minValue(0)
                ->reactive()
                ->helperText(function ($state, $get) {
                    $tyreId = $get('tyre_id');
                    $fromShowroomId = $get('from_showroom_id');

                    if (!$tyreId || !$fromShowroomId) {
                        return 'Select a source showroom and tyre to see available stock.';
                    }

                    $tyre = Tyre::find($tyreId);

                    if (!$tyre) {
                        return 'Tyre not found.';
                    }

                    $stock = $tyre->getStockByShowroom($fromShowroomId);

                    return "Available stock: {$stock}";
                })
                ->rule(function ($get) {
                    return function ($attribute, $value, $fail) use ($get) {
                        $tyreId = $get('tyre_id');
                        $fromShowroomId = $get('from_showroom_id');

                        if (!$tyreId || !$fromShowroomId) {
                            return;
                        }

                        $tyre = Tyre::find($tyreId);

                        if (!$tyre) {
                            return;
                        }

                        $stock = $tyre->getStockByShowroom($fromShowroomId);

                        if ($value > $stock) {
                            $fail("Only {$stock} tyres available in {$tyre->tyre_size} {$tyre->pattern} at source showroom.");
                        }
                    };
                }),

            DatePicker::make('date')
                ->label('Transfer Date')
                ->default(today())
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->placeholder('Add notes about this transfer...')
                ->rows(3),

        ]);
    }
}
