<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BidItemResource\Pages;

use App\Filament\Resources\BidItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBidItem extends EditRecord
{
    protected static string $resource = BidItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
