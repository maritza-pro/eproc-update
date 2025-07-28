<?php

declare(strict_types=1);

namespace App\Filament\Resources\SurveyCategoryResource\Pages;

use App\Filament\Resources\SurveyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSurveyCategories extends ListRecords
{
    protected static string $resource = SurveyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
