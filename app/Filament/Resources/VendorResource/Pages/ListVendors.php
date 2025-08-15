<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    /**
     * Get the header actions.
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
     * Mount the page.
     *
     * Redirects users based on their permissions and vendor association.
     */
    public function mount(): void
    {
        parent::mount();

        $user = Auth::user();

        if (! $user?->can(VendorResource::getModelLabel() . '.withoutGlobalScope')) {
            if ($user?->vendor) {
                $this->redirect(VendorResource::getUrl('view', ['record' => $user->vendor->getKey()]));

                return;
            }

            $this->redirect(VendorResource::getUrl('create'));

            return;
        }
    }
}
