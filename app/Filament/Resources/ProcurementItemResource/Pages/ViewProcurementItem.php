<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementItemResource\Pages;

use App\Filament\Resources\ProcurementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProcurementItem extends ViewRecord
{
    protected static string $resource = ProcurementItemResource::class;

    /**
     * Get the header actions.
     *
     * Defines the actions available in the record header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
            Actions\EditAction::make(),
        ];
    }
}
