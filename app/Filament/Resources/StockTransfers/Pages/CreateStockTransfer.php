<?php

namespace App\Filament\Resources\StockTransfers\Pages;

use App\Filament\Resources\StockTransfers\Schemas\StockTransferForm;
use App\Filament\Resources\StockTransfers\StockTransferResource;
use App\Models\StockTransfer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateStockTransfer extends CreateRecord
{
    protected static string $resource = StockTransferResource::class;

    public function form(Schema $schema): Schema
    {
        return StockTransferForm::configure($schema);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $items = $data['transfer_items'] ?? [];
        unset($data['transfer_items']);

        // Backward compatibility in case only single-item fields are posted.
        if (empty($items) && filled($data['tyre_id'] ?? null) && filled($data['quantity'] ?? null)) {
            $items[] = [
                'tyre_id' => $data['tyre_id'],
                'quantity' => $data['quantity'],
            ];
        }

        $firstCreated = null;
        $batchId = (string) Str::uuid();
        $hasBatchIdColumn = SchemaFacade::hasColumn('stock_transfers', 'batch_id');

        DB::transaction(function () use ($data, $items, $batchId, $hasBatchIdColumn, &$firstCreated) {
            foreach ($items as $item) {
                $payload = [
                    'from_showroom_id' => $data['from_showroom_id'],
                    'to_showroom_id' => $data['to_showroom_id'],
                    'tyre_id' => $item['tyre_id'],
                    'quantity' => $item['quantity'],
                    'date' => $data['date'],
                    'notes' => $data['notes'] ?? null,
                ];

                if ($hasBatchIdColumn) {
                    $payload['batch_id'] = $batchId;
                }

                $created = StockTransfer::query()->create($payload);

                $firstCreated ??= $created;
            }
        });

        if (!$firstCreated) {
            throw new \RuntimeException('At least one tyre is required for stock transfer creation.');
        }

        return $firstCreated;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('create');
    }
}
