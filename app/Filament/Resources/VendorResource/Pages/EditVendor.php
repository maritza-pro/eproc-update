<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Hexters\HexaLite\Models\HexaRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($data): array {
            // TODO : If condition is always true.
            if ($record = $this->getRecord()) {

                if ($record->verification_status === VendorStatus::Pending &&
                    isset($data['verification_status']) &&
                    $data['verification_status'] !== VendorStatus::Pending->value) {

                    $data['verified_by'] = Auth::id();

                    $data['verified_at'] = now();
                    $roleId = HexaRole::where('name', 'Vendor')->value('id');
                    $record->user->roles()->syncWithoutDetaching([$roleId]);
                }
            }

            return $data;
        });
    }
}
