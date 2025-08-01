<?php

namespace App\Filament\Resources\BankVendorResource\Pages;

use App\Filament\Resources\BankVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBankVendor extends ViewRecord
{
    protected static string $resource = BankVendorResource::class;

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
