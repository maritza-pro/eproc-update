<?php

declare(strict_types = 1);

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the list header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
