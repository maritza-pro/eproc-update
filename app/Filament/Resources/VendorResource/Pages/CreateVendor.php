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

    /**
     * Get the cancel form action.
     *
     * Returns the cancel action, hidden if the user is not a superuser.
     */
    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()->hidden(! $this->isSuper());
    }

    /**
     * Get the "create another" form action.
     *
     * Modifies the create another action to be hidden for non-super users.
     */
    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden(! $this->isSuper());
    }

    /**
     * Get the create form action.
     *
     * Modifies the create action label and confirmation based on user role.
     */
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

    /**
     * Get the form actions alignment.
     *
     * Defines the alignment of the form actions (buttons).
     */
    public function getFormActionsAlignment(): string
    {
        return 'right';
    }

    /**
     * Get the title of the page.
     *
     * Returns the page title based on the user's super status.
     */
    public function getTitle(): string
    {
        return $this->isSuper() ? 'Create Vendor' : 'Register Vendor';
    }

    /**
     * Checks if the current user is a super user.
     * Determines super user status based on permission.
     */
    private function isSuper(): bool
    {
        return Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope') ?? false;
    }
}
