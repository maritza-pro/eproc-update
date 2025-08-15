<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorTypeResource\Pages;

use App\Filament\Resources\VendorTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendorType extends ViewRecord
{
    protected static string $resource = VendorTypeResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the record view header.
     */
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
