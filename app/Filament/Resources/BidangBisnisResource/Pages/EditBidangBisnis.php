<?php

declare(strict_types=1);

namespace App\Filament\Resources\BidangBisnisResource\Pages;

use App\Filament\Resources\BidangBisnisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBidangBisnis extends EditRecord
{
    protected static string $resource = BidangBisnisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
