<?php

declare(strict_types = 1);

namespace App\Filament\Clusters\MasterProcurements\Resources\ProcurementAgendaResource\Pages;

use App\Filament\Clusters\MasterProcurements\Resources\ProcurementAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProcurementAgendas extends ManageRecords
{
    protected static string $resource = ProcurementAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
