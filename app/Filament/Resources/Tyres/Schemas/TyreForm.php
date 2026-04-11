<?php

namespace App\Filament\Resources\Tyres\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class TyreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('tyre_size')
                    ->label('Tyre Size')
                    ->required(),

                TextInput::make('pattern')
                    ->label('Pattern'),

                TextInput::make('category')
                    ->label('Category'),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->prefix('₹')
                    ->required(),

            ]);
    }
}