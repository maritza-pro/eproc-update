<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the record header.
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
