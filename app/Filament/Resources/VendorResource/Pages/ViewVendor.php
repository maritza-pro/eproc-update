<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    /**
     * Get the actions for the header.
     *
     * Defines actions available in the record view header.
     */
    protected function getHeaderActions(): array
    {
        $isSuper = $this->isSuper();

        return [
            Actions\Action::make('back')
            ->label('Back')
            ->icon('heroicon-m-arrow-left')
            ->color('gray')
            ->url(static::getResource()::getUrl('index'))
            ->hidden(! $isSuper),

            Actions\EditAction::make(),

            Actions\Action::make('resubmit')
            ->label('Resubmit Verification')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Rejected && ! $isSuper)
            ->requiresConfirmation()
            // TODO : There's a typo in the modal heading. 'Verfication' should be 'Verification'.
            ->modalHeading('Resubmit Vendor Verification?')
            ->modalDescription('Your vendor information will be reopened for updates and sent for review again. Do you want to continue?')
            ->modalSubmitActionLabel('Yes, Resubmit')
            ->action(function ($record) {
                $record->update([
                    'verification_status' => VendorStatus::Pending,
                ]);

                Notification::make()
                    ->title('Your vendor verification has been resubmitted.')
                    ->success()
                    ->send();
            }),

            Actions\Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Pending && $isSuper)
            ->requiresConfirmation()
            ->modalHeading('Reject Vendor Verification?')
            ->modalDescription('This action will mark the vendor as rejected, and they will need to resubmit their information. Are you sure?')
            ->modalSubmitActionLabel('Yes, Reject')
            ->form([
                Textarea::make('rejection_reason')
                    ->label('Reason for Rejection')
                    ->required()
                    ->rows(5)
                    ->maxLength(500),
            ])
            ->action(function ($record, array $data): void {
                $record->update([
                    'verification_status' => VendorStatus::Rejected,
                    'rejection_reason' => $data['rejection_reason'],
                ]);

                $this->refreshFormData([
                    'rejection_reason',
                ]);

                Notification::make()
                    ->title('Vendor has been rejected.')
                    ->success()
                    ->send();
            }),

            Actions\Action::make('approve')
            ->label('Approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Pending && $isSuper)
            ->requiresConfirmation()
            ->modalHeading('Approve Vendor Verification?')
            ->modalDescription('Once approved, this vendor will be marked as verified and can access the system. Are you sure?')
            ->modalSubmitActionLabel('Yes, Approve')
            ->action(function ($record): void {
                $record->update([
                    'verification_status' => VendorStatus::Approved,
                ]);

                Notification::make()
                    ->title('Vendor has been approved successfully.')
                    ->success()
                    ->send();
            }),
        ];
    }

    /**
     * Checks if the current user is a super user.
     * Determines if the user has permission to bypass global scopes.
     */
    private function isSuper(): bool
    {
        return Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope') ?? false;
    }
}
