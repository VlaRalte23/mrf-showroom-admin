<?php

namespace App\Filament\Resources\Invoices\Schemas;

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
                ->options(Showroom::pluck('name','id'))
                ->searchable()
                ->required(),

            TextInput::make('invoice_no')
                ->label('Invoice Number')
                ->required(),

            DatePicker::make('date')
                ->required(),

            Repeater::make('items')
                ->relationship()
                ->schema([

                    Select::make('tyre_id')
                        ->label('Tyre')
                        ->options(
                            Tyre::pluck('tyre_size','id')
                        )
                        ->searchable()
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