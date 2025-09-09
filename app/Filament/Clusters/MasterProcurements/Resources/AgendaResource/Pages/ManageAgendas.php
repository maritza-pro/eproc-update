<?php

declare(strict_types = 1);

namespace App\Filament\Clusters\MasterProcurements\Resources\AgendaResource\Pages;

use App\Filament\Clusters\MasterProcurements\Resources\AgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

// TODO : @rizkyxp Agenda / Agendas coba diskus sama maup yee
class ManageAgendas extends ManageRecords
{
    protected static string $resource = AgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
