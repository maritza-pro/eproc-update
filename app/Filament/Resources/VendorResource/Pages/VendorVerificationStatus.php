<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource;
use App\Models\Vendor;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Auth;

class VendorVerificationStatus extends Page implements HasForms
{
    use HasUnsavedDataChangesAlert, InteractsWithForms, InteractsWithRecord;

    protected static string $resource = VendorResource::class;

    protected static ?string $title = 'Verification Status';

    protected static string $view = 'filament.resources.vendor-resource.pages.vendor-verification-status';

    public ?array $data = [];

    protected function getFormActions(): array
    {
        $withoutGlobalScope = Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope');

        return [
            FormActions\Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->outlined()
                ->url(fn () => static::getResource()::getUrl('view', ['record' => $this->getRecord()]))
                ->visible($withoutGlobalScope),
            FormActions\Action::make('save')
                ->label('Save')
                ->submit('save')
                ->visible($withoutGlobalScope),
        ];
    }

    protected function getHeaderActions(): array
    {
        $withoutGlobalScope = Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope');

        return
            [
                Actions\Action::make('submit')
                    ->label('Submit Verification')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->modalHeading('')
                    ->modalContent(fn () => view('filament.forms.components.statement-and-agreement'))
                    ->modalWidth('3xl')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->form([
                        Forms\Components\Checkbox::make('agreement')
                            ->label('By checking this box, you acknowledge that you have read, understood, and agree to the Statement & Agreement above.')
                            ->accepted()
                            ->required(),
                    ])
                    ->action(function ($record) {
                        $record->update([
                            'verification_status' => VendorStatus::Pending,
                        ]);

                        Notification::make()
                            ->title('Your vendor verification has been submitted.')
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('resubmit')
                    ->label('Resubmit Verification')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Rejected && ! $withoutGlobalScope && ! $record->is_blacklisted)
                    ->requiresConfirmation()
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
            ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        $withoutGlobalScope = Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope');

        $isRejected = fn (Get $get): bool => $get('verification_status') === VendorStatus::Rejected->value;

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->hidden($withoutGlobalScope)
                    ->schema([
                        Forms\Components\Textarea::make('blacklist_reason')
                            ->label('ⓘ Vendor is BLACKLISTED')
                            ->disabled()
                            ->hidden(fn (?Vendor $record): bool => ! $record?->is_blacklisted)
                            ->autosize(),
                        Forms\Components\Fieldset::make('ⓘ Verification Status')
                            ->schema([
                                Forms\Components\View::make('verification_status')
                                    ->view('filament.forms.components.status-badge')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('rejection_reason')
                                    ->label('')
                                    ->disabled()
                                    ->autosize()
                                    ->columnSpanFull()
                                    ->helperText('Please check the notes above and update your details before resubmitting.')
                                    ->visible(fn (?Vendor $record): bool => $record !== null && $record->verification_status === VendorStatus::Rejected && ! $record->is_blacklisted),
                            ])
                            ->hidden(fn (?Vendor $record) => $record?->is_blacklisted)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Card::make()
                    ->hidden(! $withoutGlobalScope)
                    ->schema([
                        Forms\Components\ToggleButtons::make('verification_status')
                            ->inline()
                            ->live()
                            ->enum(VendorStatus::class)
                            ->options(fn (): array => [
                                VendorStatus::Approved->value => VendorStatus::Approved->getLabel(),
                                VendorStatus::Rejected->value => VendorStatus::Rejected->getLabel(),
                            ])
                            ->colors([
                                VendorStatus::Approved->value => VendorStatus::Approved->getColor(),
                                VendorStatus::Rejected->value => VendorStatus::Rejected->getColor(),
                            ])
                            ->icons([
                                VendorStatus::Approved->value => VendorStatus::Approved->getIcon(),
                                VendorStatus::Rejected->value => VendorStatus::Rejected->getIcon(),
                            ]),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->rows(5)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible($isRejected)
                            ->required($isRejected),
                    ]),
                Forms\Components\Card::make()
                    ->hidden(! $withoutGlobalScope)
                    ->schema([
                        Forms\Components\Toggle::make('is_blacklisted')
                            ->label('⚠️ Blacklist Vendor')
                            ->live()
                            ->helperText('Marking this vendor as *blacklisted* will block them from participating in all procurements.')
                            ->onColor('danger'),
                        Forms\Components\Textarea::make('blacklist_reason')
                            ->label('Blacklist Reason')
                            ->rows(5)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn (Get $get): bool => (bool) $get('is_blacklisted'))
                            ->required(fn (Get $get): bool => (bool) $get('is_blacklisted')),
                    ]),
            ])
            ->statePath('data')
            // @phpstan-ignore argument.type
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var \App\Models\Vendor $vendor */
        $vendor = $this->getRecord();

        $vendor->fill($data);
        $vendor->save();

        Notification::make()
            ->title('Verification Status updated successfully')
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Verification Status';
    }
}
