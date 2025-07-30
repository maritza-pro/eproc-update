<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SurveyCategoryResource\Pages;

use App\Filament\Resources\SurveyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSurveyCategory extends EditRecord
{
    protected static string $resource = SurveyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
