<?php

namespace App\Filament\Resources\Tyres\Schemas;

use App\Models\Tyre;
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
                    ->required()
                    ->live(onBlur: true)
                    ->rule(function ($get) {
                        return function (string $attribute, $value, $fail) use ($get) {
                            $record = request()->route('record');
                            $recordId = is_object($record) ? $record->getKey() : $record;

                            $size = strtolower(trim((string) $value));
                            $pattern = strtolower(trim((string) ($get('pattern') ?? '')));

                            $exists = Tyre::query()
                                ->whereRaw('LOWER(tyre_size) = ?', [$size])
                                ->whereRaw('LOWER(COALESCE(pattern, "")) = ?', [$pattern])
                                ->when($recordId, fn ($query) => $query->whereKey('!=', $recordId))
                                ->exists();

                            if ($exists) {
                                $fail('This tyre name already exists.');
                            }
                        };
                    }),

                TextInput::make('pattern')
                    ->label('Pattern')
                    ->live(onBlur: true)
                    ->rule(function ($get) {
                        return function (string $attribute, $value, $fail) use ($get) {
                            $record = request()->route('record');
                            $recordId = is_object($record) ? $record->getKey() : $record;

                            $size = strtolower(trim((string) ($get('tyre_size') ?? '')));
                            $pattern = strtolower(trim((string) $value));

                            if ($size === '') {
                                return;
                            }

                            $exists = Tyre::query()
                                ->whereRaw('LOWER(tyre_size) = ?', [$size])
                                ->whereRaw('LOWER(COALESCE(pattern, "")) = ?', [$pattern])
                                ->when($recordId, fn ($query) => $query->whereKey('!=', $recordId))
                                ->exists();

                            if ($exists) {
                                $fail('This tyre name already exists.');
                            }
                        };
                    }),

                TextInput::make('category')
                    ->label('Category'),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->prefix('₹')
                    ->minValue(0)
                    ->required(),

            ]);
    }
}