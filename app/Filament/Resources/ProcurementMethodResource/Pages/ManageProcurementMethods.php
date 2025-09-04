<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementMethodResource\Pages;

use App\Filament\Resources\ProcurementMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProcurementMethods extends ManageRecords
{
    protected static string $resource = ProcurementMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
