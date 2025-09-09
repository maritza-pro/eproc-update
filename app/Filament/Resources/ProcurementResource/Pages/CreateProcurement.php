<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\ProcurementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProcurement extends CreateRecord
{
    protected static string $resource = ProcurementResource::class;

    public function getFormActionsAlignment(): string
    {
        return 'right';
    }
}
