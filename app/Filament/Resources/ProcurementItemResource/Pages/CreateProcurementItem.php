<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProcurementItemResource\Pages;

use App\Filament\Resources\ProcurementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProcurementItem extends CreateRecord
{
    protected static string $resource = ProcurementItemResource::class;
}
