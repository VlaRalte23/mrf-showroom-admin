<?php

namespace App\Filament\Resources\StockTransfers\Pages;

use App\Filament\Resources\StockTransfers\Schemas\StockTransferForm;
use App\Filament\Resources\StockTransfers\StockTransferResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditStockTransfer extends EditRecord
{
    protected static string $resource = StockTransferResource::class;

    public function form(Schema $schema): Schema
    {
        return StockTransferForm::configure($schema);
    }
}
