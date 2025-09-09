<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BusinessFieldResource\Pages;

use App\Filament\Resources\BusinessFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessField extends EditRecord
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
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
