<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('User')
                ->options(User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),

            Select::make('attendance_type')
                ->label('Attendance Type')
                ->options([
                    'clock_in' => 'Clock In',
                    'clock_out' => 'Clock Out',
                ])
                ->default('clock_in')
                ->required(),

            TextInput::make('latitude')
                ->label('Latitude')
                ->numeric()
                ->required(),

            TextInput::make('longitude')
                ->label('Longitude')
                ->numeric()
                ->required(),

            TextInput::make('accuracy')
                ->label('GPS Accuracy (meters)')
                ->numeric()
                ->nullable(),

            TextInput::make('location_text')
                ->label('Location Description')
                ->nullable(),

            Toggle::make('is_within_geofence')
                ->label('Within Geofence')
                ->default(true),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3)
                ->nullable(),
        ]);
    }
}
