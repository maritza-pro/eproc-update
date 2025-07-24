<?php

declare(strict_types=1);

namespace App\Filament\Resources\BusinessFieldResource\Pages;

use App\Filament\Resources\BusinessFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessField extends EditRecord
{
    protected static string $resource = BusinessFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
