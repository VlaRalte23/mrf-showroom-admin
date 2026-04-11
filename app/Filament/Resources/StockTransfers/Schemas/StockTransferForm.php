<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use App\Models\Showroom;
use App\Models\Tyre;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class StockTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('from_showroom_id')
                ->label('From Showroom')
                ->options(Showroom::pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive(),

            Select::make('to_showroom_id')
                ->label('To Showroom')
                ->options(Showroom::pluck('name', 'id'))
                ->searchable()
                ->required(),

            Select::make('tyre_id')
                ->label('Tyre')
                ->options(
                    Tyre::query()
                        ->pluck(DB::raw("CONCAT(tyre_size,' ',pattern)"), 'id')
                )
                ->searchable()
                ->required()
                ->reactive(),

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->required()
                ->min(0)
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
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->placeholder('Add notes about this transfer...')
                ->rows(3),

        ]);
    }
}
