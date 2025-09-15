<?php

declare(strict_types=1);

namespace App\Filament\Clusters\MasterProcurements\Resources\ProcurementMethodResource\Pages;

use App\Filament\Clusters\MasterProcurements\Resources\ProcurementMethodResource;
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
