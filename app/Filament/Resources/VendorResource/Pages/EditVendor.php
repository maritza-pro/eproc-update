<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($record = $this->getRecord()) {

            if ($record->verification_status === VendorStatus::Pending &&
                isset($data['verification_status']) &&
                $data['verification_status'] !== VendorStatus::Pending->value) {

                $data['verified_by'] = auth()->id();

                $data['verified_at'] = now();
            }
        }

        return $data;
    }
}
