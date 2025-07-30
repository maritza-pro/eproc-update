<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorBusinessResource\Pages;

use App\Filament\Resources\VendorBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorBusiness extends EditRecord
{
    protected static string $resource = VendorBusinessResource::class;

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
