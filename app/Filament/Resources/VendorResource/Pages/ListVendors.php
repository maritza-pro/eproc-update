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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', VendorStatus::Approved)
                    ->where('is_blacklisted', false)),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', VendorStatus::Pending)
                    ->where('is_blacklisted', false)),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('verification_status', VendorStatus::Rejected)
                    ->where('is_blacklisted', false)),
            'blacklisted' => Tab::make('Blacklisted')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_blacklisted', true)),
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
