<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SurveyCategoryResource\Pages;

use App\Filament\Resources\SurveyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSurveyCategory extends ViewRecord
{
    protected static string $resource = SurveyCategoryResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the record view header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label((string) __('Back'))
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
            Actions\EditAction::make(),
        ];
    }
}
