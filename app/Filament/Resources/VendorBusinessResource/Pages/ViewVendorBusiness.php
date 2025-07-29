<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorBusinessResource\Pages;

use App\Filament\Resources\VendorBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendorBusiness extends ViewRecord
{
    protected static string $resource = VendorBusinessResource::class;

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
