<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    private function isSuper(): bool
    {
        return Auth::user()?->can(UserResource::getModelLabel() . '.withoutGlobalScope') ?? false;
    }

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
        ];
    }
}
