<?php

namespace App\Filament\Resources\Debts;

use App\Models\Debt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\Debts\Schemas\DebtForm;
use App\Filament\Resources\Debts\Tables\DebtsTable;
use App\Filament\Resources\Debts\Pages\ListDebts;
use App\Filament\Resources\Debts\Pages\CreateDebt;
use App\Filament\Resources\Debts\Pages\EditDebt;
use Illuminate\Database\Eloquent\Builder;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $recordTitleAttribute = 'customer_name';

    protected static ?string $navigationLabel = 'Debts';

    protected static ?string $modelLabel = 'Debt';

    protected static ?string $pluralModelLabel = 'Debts';

    public static function form(Schema $schema): Schema
    {
        return DebtForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DebtsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['sale.showroom', 'sale.items.tyre']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDebts::route('/'),
            'create' => CreateDebt::route('/create'),
            'edit' => EditDebt::route('/{record}/edit'),
        ];
    }
}