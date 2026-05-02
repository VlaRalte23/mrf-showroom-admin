<?php

namespace App\Filament\Resources\Debts\Pages;

use App\Filament\Resources\Debts\DebtResource;
use App\Models\Sale;
use Filament\Resources\Pages\CreateRecord;

class CreateDebt extends CreateRecord
{
    protected static string $resource = DebtResource::class;

    public function mount(): void
    {
        parent::mount();

        $saleId = request()->integer('sale_id');

        if (!$saleId) {
            return;
        }

        $sale = Sale::with('items')->find($saleId);

        if (!$sale) {
            return;
        }

        $total = $sale->items->sum(fn ($item) => $item->quantity * $item->price);

        $this->form->fill([
            'sale_id' => $sale->id,
            'amount' => $total,
            'paid_amount' => 0,
            'remaining_amount' => $total,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $saleId = request()->integer('sale_id');

        if ($saleId) {
            $data['sale_id'] = $saleId;
        }

        return $data;
    }
}