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

    /**
     * Get the actions for the header.
     *
     * Defines the actions available in the list records header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Mount the list page.
     *
     * Redirects users without permission to their own view.
     */
    public function mount(): void
    {
        parent::mount();

        $user = Auth::user();

        if (! $user->can(UserResource::getModelLabel() . '.withoutGlobalScope') && $user) {
            /** @var \App\Models\User $user */
            $this->redirect(UserResource::getUrl('view', ['record' => $user->getKey()])
            );
        }
    }
}
