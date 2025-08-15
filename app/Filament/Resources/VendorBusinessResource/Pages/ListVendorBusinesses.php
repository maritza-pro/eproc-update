<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorBusinessResource\Pages;

use App\Filament\Resources\VendorBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorBusinesses extends ListRecords
{
    protected static string $resource = VendorBusinessResource::class;

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
