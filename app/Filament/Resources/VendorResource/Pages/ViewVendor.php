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
                ->label((string) __('Back'))
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index'))
                ->hidden(! $isSuper),

            Actions\EditAction::make(),

            Actions\Action::make('resubmit')
                ->label((string) __('Resubmit Verification'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Rejected && ! $isSuper && ! $record->is_blacklisted)
                ->requiresConfirmation()
                ->modalHeading('Resubmit Vendor Verification?')
                ->modalDescription('Your vendor information will be reopened for updates and sent for review again. Do you want to continue?')
                ->modalSubmitActionLabel('Yes, Resubmit')
                ->action(function ($record) {
                    $record->update([
                        'verification_status' => VendorStatus::Pending,
                    ]);

                    Notification::make()
                        ->title((string) __('Your vendor verification has been resubmitted.'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('blacklist')
                ->label((string) __('Blacklist Vendor'))
                ->icon('heroicon-o-no-symbol')
                ->color('blacklist')
                ->visible(fn ($record): bool => $isSuper && ! $record->is_blacklisted)
                ->requiresConfirmation()
                ->modalHeading('Blacklist this vendor?')
                ->modalDescription('This will mark the vendor as blacklisted and block them from participating in procurements.')
                ->modalSubmitActionLabel('Yes, Blacklist')
                ->form([
                    Textarea::make('blacklist_reason')
                        ->label((string) __('Reason for Blacklisting'))
                        ->required()
                        ->rows(5)
                        ->maxLength(500),
                ])
                ->action(function ($record, array $data): void {
                    $record->update([
                        'is_blacklisted' => true,
                        'blacklist_reason' => $data['blacklist_reason'],
                    ]);

                    $this->refreshFormData([
                        'blacklist_reason',
                    ]);

                    Notification::make()
                        ->title((string) __('Vendor has been blacklisted.'))
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('unblacklist')
                ->label((string) __('Unblacklist Vendor'))
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->visible(fn ($record): bool => $isSuper && $record->is_blacklisted)
                ->requiresConfirmation()
                ->modalHeading('Remove from blacklist?')
                ->modalDescription('This will allow the vendor to participate again.')
                ->modalSubmitActionLabel('Yes, Unblacklist')
                ->action(function ($record): void {
                    $record->update([
                        'is_blacklisted' => false,
                        'blacklist_reason' => null,
                    ]);

                    Notification::make()
                        ->title((string) __('Vendor has been removed from blacklist.'))
                        ->success()
                        ->send();

                }),

            Actions\Action::make('reject')
                ->label((string) __('Reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Pending && $isSuper)
                ->requiresConfirmation()
                ->modalHeading('Reject Vendor Verification?')
                ->modalDescription('This action will mark the vendor as rejected, and they will need to resubmit their information. Are you sure?')
                ->modalSubmitActionLabel('Yes, Reject')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label((string) __('Reason for Rejection'))
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
                        ->title((string) __('Vendor has been rejected.'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('approve')
                ->label((string) __('Approve'))
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
                        ->title((string) __('Vendor has been approved successfully.'))
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
