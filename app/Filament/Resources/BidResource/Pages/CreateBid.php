<?php

declare(strict_types=1);

namespace App\Filament\Resources\BidResource\Pages;

use App\Filament\Resources\BidResource;
use App\Models\BidItem;
use Filament\Resources\Pages\CreateRecord;

class CreateBid extends CreateRecord
{
    protected static string $resource = BidResource::class;

    /**
     * Perform actions after bid creation.
     *
     * Creates BidItem records for each procurement item associated with the bid.
     */
    protected function afterCreate(): void
    {
        /** @var \App\Models\Bid $bid */
        $bid = $this->record;

        $procurementItems = $bid->procurement->items;

        foreach ($procurementItems as $item) {
            BidItem::query()->create([
                'bid_id' => $bid->id,
                'procurement_item_id' => $item->id,
                'offered_quantity' => $item->quantity,
                'unit_price' => 0,
                'notes' => '',
            ]);
        }
    }
}
