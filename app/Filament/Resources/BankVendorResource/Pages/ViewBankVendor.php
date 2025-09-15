<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankVendorResource\Pages;

use App\Filament\Resources\BankVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBankVendor extends ViewRecord
{
    protected static string $resource = BankVendorResource::class;

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
