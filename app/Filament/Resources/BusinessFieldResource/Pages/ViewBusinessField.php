<?php

declare(strict_types=1);

namespace App\Filament\Resources\BusinessFieldResource\Pages;

use App\Filament\Resources\BusinessFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBusinessField extends ViewRecord
{
    protected static string $resource = BusinessFieldResource::class;

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
