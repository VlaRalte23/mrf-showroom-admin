<?php

namespace App\Filament\Resources\StockTransfers\Pages;

use App\Filament\Resources\StockTransfers\Schemas\StockTransferForm;
use App\Filament\Resources\StockTransfers\StockTransferResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateStockTransfer extends CreateRecord
{
    protected static string $resource = StockTransferResource::class;

    public function form(Schema $schema): Schema
    {
        return StockTransferForm::configure($schema);
    }
}
