<?php

declare(strict_types=1);

namespace App\Filament\Resources\BidItemResource\Pages;

use App\Filament\Resources\BidItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBidItems extends ListRecords
{
    protected static string $resource = BidItemResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the list records header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
