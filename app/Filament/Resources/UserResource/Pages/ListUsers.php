<?php

declare(strict_types = 1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function mount(): void
    {
        parent::mount();

        $user = Auth::user();

        if (! $user?->can(UserResource::getModelLabel() . '.withoutGlobalScope')) {
            $this->redirect(
                UserResource::getUrl('view', ['record' => $user->getKey()])
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
