<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Showroom;
use App\Models\Tyre;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('showroom_id')
                ->label('Showroom')
                ->options(Showroom::pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive(),

            DatePicker::make('date')
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
        ->pluck(DB::raw("CONCAT(tyre_size,' ',pattern)"), 'id')
)
                        ->searchable()
                        ->required()
                        ->reactive(),

                    TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->min(0)
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
            ->min(0)
            ->prefix('₹')
            ->required(),


                ])
                ->columns(2)

        ]);
    }
}