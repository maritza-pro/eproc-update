<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BusinessFieldResource\Pages;

use App\Filament\Resources\BusinessFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessFields extends ListRecords
{
    protected static string $resource = BusinessFieldResource::class;

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
