<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BidResource\Pages;

use App\Filament\Resources\BidResource;
use App\Models\BidItem;
use Filament\Resources\Pages\CreateRecord;

class CreateBid extends CreateRecord
{
    protected static string $resource = BidResource::class;

    protected function afterCreate(): void
    {
        $bid = $this->record;

        $procurementItems = $bid->procurement->items;

        foreach ($procurementItems as $item) {
            BidItem::create([
                'bid_id' => $bid->id,
                'procurement_item_id' => $item->id,
                'offered_quantity' => $item->quantity,
                'unit_price' => 0,
                'notes' => '',
            ]);
        }
    }
}
