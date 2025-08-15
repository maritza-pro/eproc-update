<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorTypeResource\Pages;

use App\Filament\Resources\VendorTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorTypes extends ListRecords
{
    protected static string $resource = VendorTypeResource::class;

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
