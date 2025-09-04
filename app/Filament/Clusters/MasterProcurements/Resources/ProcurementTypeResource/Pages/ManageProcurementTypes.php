<?php

declare(strict_types = 1);

namespace App\Filament\Clusters\MasterProcurements\Resources\ProcurementTypeResource\Pages;

use App\Filament\Clusters\MasterProcurements\Resources\ProcurementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProcurementTypes extends ManageRecords
{
    protected static string $resource = ProcurementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
