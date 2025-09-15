<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class VendorFinancial extends Page implements HasForms
{
    use HasUnsavedDataChangesAlert, InteractsWithForms, InteractsWithRecord;

    protected static string $resource = VendorResource::class;

    protected static ?string $title = 'Financial';

    protected static string $view = 'filament.resources.vendor-resource.pages.vendor-financial';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_financial_report_attachment')
                    ->collection('vendor_financial_report_attachment')
                    ->maxFiles(1)
                    ->label((string) __('Last Financial Report Attachment (PDF, max 2MB)'))
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->downloadable(),
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
            ->title((string) __('Financial Report updated successfully'))
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Financial';
    }
}
