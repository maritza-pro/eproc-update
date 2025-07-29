<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorBusinessResource\Pages;

use App\Filament\Resources\VendorBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorBusinesses extends ListRecords
{
    protected static string $resource = VendorBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
