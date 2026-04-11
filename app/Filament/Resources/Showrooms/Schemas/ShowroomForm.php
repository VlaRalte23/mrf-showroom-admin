<?php

namespace App\Filament\Resources\Showrooms\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class ShowroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Showroom Name')
                    ->required(),

                TextInput::make('location')
                    ->label('Location'),

            ]);
    }
}