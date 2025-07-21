<?php

declare(strict_types=1);

namespace App\Filament\Resources\BidItemResource\Pages;

use App\Filament\Resources\BidItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBidItems extends ListRecords
{
    protected static string $resource = BidItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
