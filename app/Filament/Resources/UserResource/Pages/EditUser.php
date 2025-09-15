<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Get the actions for the header.
     *
     * Defines the actions available in the record edit header.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    /**
     * Mutates the form data before saving.
     *
     * Updates the password if a new password is provided.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = $this->data;

        if (filled($state['new_password'] ?? null)) {
            $data['password'] = $state['new_password'];
        }

        return $data;
    }
}
