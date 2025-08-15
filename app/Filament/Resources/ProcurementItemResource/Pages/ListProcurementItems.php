<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementItemResource\Pages;

use App\Filament\Resources\ProcurementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcurementItems extends ListRecords
{
    protected static string $resource = ProcurementItemResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the list records header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
