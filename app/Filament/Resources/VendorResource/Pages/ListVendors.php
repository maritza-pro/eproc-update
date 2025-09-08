<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListVendors extends ListRecords
{
    use ExposesTableToWidgets;

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

    protected function getHeaderWidgets(): array
    {
        return [
            VendorResource\Widgets\OverviewVendorWidget::class,
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
                $this->redirect(VendorResource::getUrl('company', ['record' => $user->vendor->getKey()]));

                return;
            }

            $this->redirect(VendorResource::getUrl('create'));

            return;
        }
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make((string) __('All'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', '!=', VendorStatus::Draft)
                    ->where('is_blacklisted', false)),
            'approved' => Tab::make((string) __('Approved'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', VendorStatus::Approved)
                    ->where('is_blacklisted', false)),
            'pending' => Tab::make((string) __('Pending'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', VendorStatus::Pending)
                    ->where('is_blacklisted', false)),
            'rejected' => Tab::make((string) __('Rejected'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', VendorStatus::Rejected)
                    ->where('is_blacklisted', false)),
            'blacklisted' => Tab::make((string) __('Blacklisted'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_blacklisted', true)),
        ];
    }
}
