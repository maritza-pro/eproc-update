<?php

declare(strict_types = 1);

namespace App\Filament\Clusters\MasterProcurements\Resources\ScheduleResource\Pages;

use App\Filament\Clusters\MasterProcurements\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

// TODO : @rizkyxp Agenda / Agendas coba diskus sama maup yee
class ManageSchedules extends ManageRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
