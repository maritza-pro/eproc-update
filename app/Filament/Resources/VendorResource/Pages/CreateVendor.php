<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()->hidden(! $this->isSuper());
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden(! $this->isSuper());
    }

    protected function getCreateFormAction(): Actions\Action
    {
        $isSuper = $this->isSuper();

        return parent::getCreateFormAction()
            ->label($isSuper ? 'Create' : 'Submit')
            ->requiresConfirmation()
            ->modalHeading($isSuper ? 'Create vendor?' : 'Submit registration?')
            ->modalDescription($isSuper ? 'This will create the vendor record.' : 'Please review your data before submitting.')
            ->modalSubmitActionLabel($isSuper ? 'Yes, Create' : 'Yes, Register');
    }

    public function getFormActionsAlignment(): string
    {
        return 'right';
    }

    public function getTitle(): string
    {
        return $this->isSuper() ? 'Create Vendor' : 'Register Vendor';
    }

    private function isSuper(): bool
    {
        return Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope') ?? false;
    }
}
