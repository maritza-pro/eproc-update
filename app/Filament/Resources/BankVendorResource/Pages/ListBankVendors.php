<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankVendorResource\Pages;

use App\Filament\Resources\BankVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBankVendors extends ListRecords
{
    protected static string $resource = BankVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
