<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    /**
     * Get the actions for the header.
     *
     * Defines actions available in the record view header.
     */
    protected function getHeaderActions(): array
    {
        $isSuper = $this->isSuper();

        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index'))
                ->hidden(! $isSuper),
            Actions\EditAction::make(),
            VendorResource::getResubmitAction(),
        ];
    }

    /**
     * Checks if the current user is a super user.
     * Determines if the user has permission to bypass global scopes.
     */
    private function isSuper(): bool
    {
        return Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope') ?? false;
    }
}
