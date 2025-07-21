<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BidItemResource\Pages;

use App\Filament\Resources\BidItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBidItem extends ViewRecord
{
    protected static string $resource = BidItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
            Actions\EditAction::make(),
        ];
    }
}
